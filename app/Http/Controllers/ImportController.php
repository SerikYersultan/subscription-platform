<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;

class ImportController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,pdf|max:10240',
        ], [
            'csv_file.required' => 'Please select a file to upload.',
            'csv_file.mimes' => 'Only CSV, TXT, or PDF files are allowed.',
            'csv_file.max' => 'The file must not exceed 10 MB.',
        ]);

        $file = $request->file('csv_file');
        $extension = strtolower($file->getClientOriginalExtension());
        $cardData = null;

        if ($extension === 'pdf') {
            try {
                $parser = new Parser;
                $pdf = $parser->parseFile($file->getRealPath());
                $text = $pdf->getText();

                $cardData = $this->extractCardData($text);
            } catch (\Throwable $e) {
                return back()
                    ->withErrors(['csv_file' => 'Could not read the PDF. Make sure it is a valid, non-empty PDF.'])
                    ->with('open_page', 'import');
            }

            $result = $this->importFromBankStatement($text);
        } else {
            $lines = preg_split('/\r\n|\r|\n/', trim(file_get_contents($file->getRealPath())));
            $result = $this->importFromCsv($lines);
        }

        if ($result['imported'] === 0 && $result['duplicates'] === 0) {
            return back()
                ->withErrors(['csv_file' => 'No valid transactions found. For PDF: Kaspi Bank statement required. For CSV: columns must be date, amount, merchant, description.'])
                ->with('open_page', 'import');
        }

        if ($cardData !== null) {
            $cards = session('cards', []);

            $cards[] = [
                ...$cardData,
                'id' => uniqid('card_', true),
            ];

            session(['cards' => $cards]);
        }

        $parts = ["{$result['imported']} imported"];

        if ($result['duplicates'] > 0) {
            $parts[] = "{$result['duplicates']} duplicates skipped";
        }

        if ($result['skipped'] > 0) {
            $parts[] = "{$result['skipped']} invalid rows skipped";
        }

        return redirect()->route('dashboard')
            ->with('open_page', 'import')
            ->with('import_result', $result)
            ->with('success', 'Import complete: ' . implode(', ', $parts) . '.');
    }

    private function extractCardData(string $text): ?array
    {
        $cardName = 'Kaspi Card';

        if (preg_match('/Kaspi\s+Gold/iu', $text)) {
            $cardName = 'Kaspi Gold';
        } elseif (preg_match('/Kaspi\s+Red/iu', $text)) {
            $cardName = 'Kaspi Red';
        }

        preg_match('/Card number:\s*\*?(\d{4})/iu', $text, $cardMatch);
        preg_match('/Account number:\s*([A-Z0-9]+)/iu', $text, $accountMatch);
        preg_match('/Card balance\s+\d{2}\.\d{2}\.\d{2}:\s*\+?\s*([\d\s]+,\d+)\s*₸/iu', $text, $balanceMatch);

        $owner = 'Unknown Owner';

        if (preg_match('/certify\s+that\s+(.+?)\s*,?\s*IIN/ius', $text, $ownerMatch)) {
            $owner = trim(preg_replace('/\s+/', ' ', $ownerMatch[1]));
        }

        if (empty($cardMatch[1])) {
            return null;
        }

        return [
            'name' => $cardName,
            'owner' => $owner,
            'last4' => $cardMatch[1],
            'account' => $accountMatch[1] ?? null,
            'balance' => $balanceMatch[1] ?? '0,00',
            'currency' => '₸',
        ];
    }

    private function importFromBankStatement(string $text): array
    {
        $userId = auth()->id();
        $imported = $duplicates = $skipped = 0;

        foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
            $row = $this->parseBankStatementLine($line);

            if ($row === null) {
                continue;
            }

            if (! $this->isValidRow($row['transaction_date'], $row['amount'], $row['merchant_name'])) {
                $skipped++;
                continue;
            }

            $hash = $this->makeHash($userId, $row['transaction_date'], $row['amount'], $row['merchant_name']);

            if (Transaction::where('user_id', $userId)->where('source_hash', $hash)->exists()) {
                $duplicates++;
                continue;
            }

            Transaction::create([
                ...$row,
                'user_id' => $userId,
                'currency' => 'KZT',
                'source_hash' => $hash,
            ]);

            $imported++;
        }

        return compact('imported', 'duplicates', 'skipped');
    }

    private function parseBankStatementLine(string $line): ?array
    {
        $pattern = '/^(\d{2}\.\d{2}\.\d{2})\s+([-+])\s*([\d\s]+,\d+)\s*\S*\s+'
            . '(Purchases|Transfers|Replenishment|Others|Withdrawals)\s+(.+)$/u';

        if (! preg_match($pattern, trim($line), $m)) {
            return null;
        }

        [, $rawDate, $sign, $rawAmount, $type, $details] = $m;

        [$day, $month, $year] = explode('.', $rawDate);
        $date = '20' . $year . '-' . $month . '-' . $day;

        $amount = (float) str_replace([' ', ','], ['', '.'], trim($rawAmount));

        if ($sign === '-') {
            $amount = -$amount;
        }

        return [
            'transaction_date' => $date,
            'amount' => $amount,
            'merchant_name' => trim($details),
            'description' => $type,
        ];
    }

    private function importFromCsv(array $lines): array
    {
        $userId = auth()->id();
        $imported = $duplicates = $skipped = 0;
        $headerSkipped = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (! $headerSkipped && preg_match('/^\s*date\b|^\s*[a-z_\-]+\s*,/i', $line)) {
                $headerSkipped = true;
                continue;
            }

            $cols = str_getcsv($line);

            if (count($cols) < 3) {
                $skipped++;
                continue;
            }

            [$rawDate, $rawAmount, $merchant] = $cols;
            $description = $cols[3] ?? null;

            $date = date_create(trim($rawDate));

            $cleanAmount = preg_replace('/[^0-9.,]/', '', $rawAmount);

            if (preg_match('/,\d{1,2}$/', $cleanAmount)) {
                $cleanAmount = str_replace(['.', ','], ['', '.'], $cleanAmount);
            } else {
                $cleanAmount = str_replace(',', '', $cleanAmount);
            }

            $amount = (float) $cleanAmount;

            if (str_starts_with(trim($rawAmount), '-')) {
                $amount = -abs($amount);
            }

            $merchantName = trim($merchant);

            if (! $date || ! $this->isValidRow($date->format('Y-m-d'), $amount, $merchantName)) {
                $skipped++;
                continue;
            }

            $formattedDate = $date->format('Y-m-d');
            $hash = $this->makeHash($userId, $formattedDate, $amount, $merchantName);

            if (Transaction::where('user_id', $userId)->where('source_hash', $hash)->exists()) {
                $duplicates++;
                continue;
            }

            Transaction::create([
                'user_id' => $userId,
                'merchant_name' => $merchantName,
                'description' => $description ? trim($description) : null,
                'amount' => $amount,
                'transaction_date' => $formattedDate,
                'source_hash' => $hash,
            ]);

            $imported++;
        }

        return compact('imported', 'duplicates', 'skipped');
    }

    private function isValidRow(string $date, float $amount, string $merchant): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);

        if (! $d) {
            return false;
        }

        $min = new \DateTime('2000-01-01');
        $max = new \DateTime('+1 year');

        if ($d < $min || $d > $max) {
            return false;
        }

        if ($amount == 0.0 || abs($amount) > 10_000_000) {
            return false;
        }

        if (trim($merchant) === '') {
            return false;
        }

        return true;
    }

    private function makeHash(int $userId, string $date, float $amount, string $merchant): string
    {
        return md5($userId . '|' . $date . '|' . $amount . '|' . mb_strtolower(trim($merchant)));
    }
}
