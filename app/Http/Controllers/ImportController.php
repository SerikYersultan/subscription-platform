<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;

class ImportController extends Controller
{
    public function index()
    {
        // Import UI lives inside the dashboard SPA
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

        if ($extension === 'pdf') {
            try {
                $parser = new Parser;
                $pdf = $parser->parseFile($file->getRealPath());
                $text = $pdf->getText();
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

        // Nothing usable at all
        if ($result['imported'] === 0 && $result['duplicates'] === 0) {
            return back()
                ->withErrors(['csv_file' => 'No valid transactions found. For PDF: Kaspi Bank statement required. For CSV: columns must be date, amount, merchant, description.'])
                ->with('open_page', 'import');
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

    // ── Bank statement (PDF) ─────────────────────────────────────────────────

    /**
     * Parse a Kaspi Bank (or similar) statement.
     * Expected line format: DD.MM.YY  +/-  1 234,56 ₸  Type  Details
     */
    private function importFromBankStatement(string $text): array
    {
        $userId = auth()->id();
        $imported = $duplicates = $skipped = 0;

        foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
            $row = $this->parseBankStatementLine($line);

            if ($row === null) {
                continue; // header / summary / non-transaction line — not counted as skipped
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

    /**
     * Parse one line of a Kaspi bank statement.
     * Returns null for lines that are not transaction rows.
     * Example: "09.04.26 - 2 310,00 ₸ Purchases YANDEX.GO"
     */
    private function parseBankStatementLine(string $line): ?array
    {
        $pattern = '/^(\d{2}\.\d{2}\.\d{2})\s+([-+])\s*([\d\s]+,\d+)\s*\S*\s+'
            . '(Purchases|Transfers|Replenishment|Others|Withdrawals)\s+(.+)$/u';

        if (! preg_match($pattern, trim($line), $m)) {
            return null;
        }

        [, $rawDate, $sign, $rawAmount, $type, $details] = $m;

        // DD.MM.YY → 20YY-MM-DD
        [$day, $month, $year] = explode('.', $rawDate);
        $date = '20' . $year . '-' . $month . '-' . $day;

        // "2 310,00" → 2310.00  (space = thousands sep, comma = decimal sep)
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

    // ── CSV / TXT ────────────────────────────────────────────────────────────

    /**
     * Parse a plain CSV/TXT file.
     * Expected columns: date, amount, merchant, description
     */
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

            // Skip header row (any row whose first column contains non-numeric text like "date")
            if (! $headerSkipped && preg_match('/^\s*date\b|^\s*[a-z_\-]+\s*,/i', $line)) {
                $headerSkipped = true;
                continue;
            }

            $cols = str_getcsv($line);

            if (\count($cols) < 3) {
                $skipped++;
                continue;
            }

            [$rawDate, $rawAmount, $merchant] = $cols;
            $description = $cols[3] ?? null;

            // Parse date — accepts many formats via PHP
            $date = date_create(trim($rawDate));

            // Parse amount — strip currency symbols, thousands separators;
            // use a more precise pattern to avoid "-" in unexpected positions
            $cleanAmount = preg_replace('/[^0-9.,]/', '', $rawAmount);
            // Normalise: if comma is decimal separator (e.g. "1.234,56"), keep only last separator
            if (preg_match('/,\d{1,2}$/', $cleanAmount)) {
                $cleanAmount = str_replace(['.', ','], ['', '.'], $cleanAmount);
            } else {
                $cleanAmount = str_replace(',', '', $cleanAmount);
            }
            $amount = (float) $cleanAmount;

            // Restore sign lost during cleaning
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

    // ── Shared helpers ───────────────────────────────────────────────────────

    /**
     * Validate a parsed row before storing.
     *
     * Rules:
     *  - Date must be parseable and between 2000-01-01 and one year from today.
     *  - Amount must be non-zero and ≤ 10 000 000 in absolute value.
     *  - Merchant name must be non-empty.
     */
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

    /**
     * Compute a deduplication fingerprint for a transaction row.
     * Hash is MD5 of: user_id | date | amount | lowercase merchant.
     */
    private function makeHash(int $userId, string $date, float $amount, string $merchant): string
    {
        return md5($userId . '|' . $date . '|' . $amount . '|' . mb_strtolower(trim($merchant)));
    }
}
