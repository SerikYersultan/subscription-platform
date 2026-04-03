<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SubscriptionDetectorService
{
    // Cycle definitions: name => [expected days, tolerance days]
    private const CYCLES = [
        'weekly'    => ['days' => 7,   'tolerance' => 3],
        'monthly'   => ['days' => 30,  'tolerance' => 3],
        'quarterly' => ['days' => 90,  'tolerance' => 5],
        'yearly'    => ['days' => 365, 'tolerance' => 5],
    ];

    public function detect(int $userId): Collection
    {
        $transactions = Transaction::where('user_id', $userId)
            ->orderBy('transaction_date')
            ->get();

        // Group by normalized merchant name
        $groups = $transactions->groupBy(fn ($t) => strtolower(trim($t->merchant_name ?? '')));

        $detected = collect();

        foreach ($groups as $merchantKey => $txGroup) {
            if ($merchantKey === '') {
                continue;
            }

            $result = $this->analyzeGroup($txGroup);

            if ($result === null) {
                continue;
            }

            ['cycle' => $cycle, 'confidence' => $confidence, 'amount' => $amount, 'lastDate' => $lastDate] = $result;

            if ($confidence < 40) {
                continue;
            }

            // Find or create the Merchant record for this user
            $merchant = Merchant::firstOrCreate(
                [
                    'user_id' => $userId,
                    'name'    => $txGroup->first()->merchant_name,
                ],
                [
                    'canonical_name' => $merchantKey,
                ]
            );

            $cycleDays = self::CYCLES[$cycle]['days'];
            $nextBillingDate = Carbon::parse($lastDate)->addDays($cycleDays)->toDateString();

            $subscription = Subscription::updateOrCreate(
                [
                    'user_id'     => $userId,
                    'merchant_id' => $merchant->id,
                    'billing_cycle' => $cycle,
                ],
                [
                    'name'             => $merchant->canonical_name ?? $merchant->name,
                    'amount'           => $amount,
                    'currency'         => $txGroup->first()->currency ?? 'USD',
                    'status'           => 'active',
                    'confidence_score' => $confidence,
                    'next_billing_date' => $nextBillingDate,
                    'detected_at'      => now(),
                ]
            );

            $detected->push($subscription);
        }

        return $detected;
    }

    private function analyzeGroup(Collection $txGroup): ?array
    {
        $count = $txGroup->count();

        if ($count < 2) {
            return null;
        }

        $sorted = $txGroup->sortBy('transaction_date')->values();

        // Calculate intervals in days between consecutive transactions
        $intervals = [];
        for ($i = 1; $i < $sorted->count(); $i++) {
            $prev = Carbon::parse($sorted[$i - 1]->transaction_date);
            $curr = Carbon::parse($sorted[$i]->transaction_date);
            $intervals[] = $prev->diffInDays($curr);
        }

        // Detect the most likely billing cycle
        $detectedCycle = $this->detectCycle($intervals);

        // Calculate confidence score
        $confidence = 0;

        // Base: 2+ occurrences
        $confidence += 30;

        // Amount consistency: max 5% variance
        $amounts = $sorted->pluck('amount')->map(fn ($a) => (float) $a);
        $avgAmount = $amounts->average();
        $maxVariance = $amounts->max() - $amounts->min();
        $variancePct = $avgAmount > 0 ? ($maxVariance / $avgAmount) : 1;
        if ($variancePct <= 0.05) {
            $confidence += 20;
        }

        // Interval regularity
        if ($detectedCycle !== null) {
            $expectedDays = self::CYCLES[$detectedCycle]['days'];
            $tolerance = self::CYCLES[$detectedCycle]['tolerance'];
            $allRegular = collect($intervals)->every(
                fn ($d) => abs($d - $expectedDays) <= $tolerance
            );
            if ($allRegular) {
                $confidence += 20;
            }
        }

        // 3+ occurrences
        if ($count >= 3) {
            $confidence += 15;
        }

        // 5+ occurrences
        if ($count >= 5) {
            $confidence += 15;
        }

        if ($detectedCycle === null) {
            return null;
        }

        // Most common amount (mode, fallback to average)
        $amountMode = $amounts->groupBy(fn ($a) => $a)
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first() ?? $avgAmount;

        $lastDate = $sorted->last()->transaction_date;

        return [
            'cycle'      => $detectedCycle,
            'confidence' => min(100, $confidence),
            'amount'     => (float) $amountMode,
            'lastDate'   => $lastDate,
        ];
    }

    private function detectCycle(array $intervals): ?string
    {
        if (empty($intervals)) {
            return null;
        }

        $avgInterval = array_sum($intervals) / count($intervals);

        foreach (self::CYCLES as $cycleName => $spec) {
            if (abs($avgInterval - $spec['days']) <= $spec['tolerance']) {
                return $cycleName;
            }
        }

        return null;
    }
}
