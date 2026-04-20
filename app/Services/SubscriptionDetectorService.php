<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SubscriptionDetectorService
{
    private const CYCLES = [
        'weekly'    => ['days' => 7,   'min' => 5,   'max' => 9],
        'monthly'   => ['days' => 30,  'min' => 27,  'max' => 33],
        'quarterly' => ['days' => 90,  'min' => 85,  'max' => 95],
        'yearly'    => ['days' => 365, 'min' => 350, 'max' => 380],
    ];

    private const MIN_TRANSACTIONS = 3;
    private const AUTO_CREATE_THRESHOLD = 65;

    private MerchantClassifier $classifier;

    public function __construct()
    {
        $this->classifier = new MerchantClassifier;
    }

    public function detect(int $userId): Collection
    {
        $transactions = Transaction::where('user_id', $userId)
            ->where('amount', '<', 0)   // subscriptions are outgoing payments only
            ->orderBy('transaction_date')
            ->get();

        $groups = $transactions->groupBy(fn ($t) => $this->makeGroupKey($t));

        $detected = collect();

        foreach ($groups as $groupKey => $txGroup) {
            if ($groupKey === '') {
                continue;
            }

            // Split by amount bucket: each distinct price point is a separate subscription.
            // Example: APPLE.COM/BILL at $0.99 (iCloud) and $9.99 (Apple Music) → two records.
            $amountGroups = $txGroup->groupBy(fn ($t) => $this->makeAmountKey((float) $t->amount));
            $multipleAmounts = $amountGroups->count() > 1;

            foreach ($amountGroups as $amountGroup) {
                $result = $this->analyzeGroup($amountGroup);

                if ($result === null || $result['confidence'] < self::AUTO_CREATE_THRESHOLD) {
                    continue;
                }

                ['cycle' => $cycle, 'confidence' => $confidence, 'amount' => $amount, 'lastDate' => $lastDate] = $result;

                $merchant = Merchant::firstOrCreate(
                    [
                        'user_id' => $userId,
                        'name' => $amountGroup->first()->merchant_name,
                    ],
                    [
                        'canonical_name' => strtolower(trim($amountGroup->first()->merchant_name ?? '')),
                    ]
                );

                $baseName = $merchant->canonical_name ?? $merchant->name;
                $currency = $amountGroup->first()->currency ?? 'USD';

                // When a merchant has several price tiers, append the amount so the
                // user can tell the subscriptions apart (e.g. "apple.com/bill · 0.99 KZT").
                $subName = $multipleAmounts
                    ? $baseName.' · '.number_format(abs($amount), 2).' '.$currency
                    : $baseName;

                $nextBillingDate = Carbon::parse($lastDate)
                    ->addDays(self::CYCLES[$cycle]['days'])
                    ->toDateString();

                // Include amount in the unique key so different price tiers
                // produce separate subscription rows.
                $subscription = Subscription::updateOrCreate(
                    [
                        'user_id'       => $userId,
                        'merchant_id'   => $merchant->id,
                        'billing_cycle' => $cycle,
                        'amount'        => $amount,
                    ],
                    [
                        'name'             => $subName,
                        'currency'         => $currency,
                        'status'           => 'active',
                        'confidence_score' => $confidence,
                        'next_billing_date' => $nextBillingDate,
                        'detected_at'      => now(),
                    ]
                );

                $detected->push($subscription);
            }
        }

        return $detected;
    }

    /**
     * Bucket key for grouping by price point.
     * Two charges belong to the same bucket if their absolute amounts round to
     * the same value at 2 decimal places.  A merchant with different prices
     * (e.g. APPLE.COM/BILL charging both $0.99 and $9.99) produces separate
     * buckets and therefore separate subscription candidates.
     */
    private function makeAmountKey(float $amount): string
    {
        return number_format(abs($amount), 2, '.', '');
    }

    /**
     * Build a stable group key from merchant name plus description when the
     * description carries real service-identity info (e.g. "Yandex" + "Plus").
     * Generic Kaspi Bank description tokens ("Purchases", "Transfers", …)
     * are treated as noise and ignored.
     */
    private function makeGroupKey(Transaction $tx): string
    {
        $name = strtolower(trim($tx->merchant_name ?? ''));
        $desc = strtolower(trim($tx->description ?? ''));

        $genericDescriptions = [
            'purchases', 'transfers', 'replenishment', 'others', 'withdrawals',
        ];

        if ($desc !== '' && ! \in_array($desc, $genericDescriptions, true)) {
            return $name.'|'.$desc;
        }

        return $name;
    }

    /**
     * Analyse one merchant group and return detection metadata, or null if the
     * group should not be treated as a subscription.
     *
     * Score breakdown (max 100):
     *   keyword signal      -60 … +35   (via MerchantClassifier)
     *   amount stability      -15 … +25
     *   interval regularity   -20 … +25
     *   transaction count       0 … +30
     *   overcharge penalty    -20 …   0
     */
    private function analyzeGroup(Collection $txGroup): ?array
    {
        $sorted = $txGroup->sortBy('transaction_date')->values();

        // Deduplicate by calendar date: if the same charge was imported twice on
        // the same day (e.g. data loaded before source_hash dedup was in place),
        // keeping both would produce a 0-day interval that breaks all scoring.
        $sorted = $sorted
            ->unique(fn ($t) => Carbon::parse($t->transaction_date)->format('Y-m-d'))
            ->values();

        $count = $sorted->count();

        if ($count < self::MIN_TRANSACTIONS) {
            return null;
        }

        $first = $sorted->first();

        $keywordScore = $this->classifier->classify(
            $first->merchant_name ?? '',
            $first->description ?? '',
        );

        if ($keywordScore <= -60) {
            return null;
        }

        if ($this->hasTooFrequentCharges($sorted)) {
            return null;
        }

        $intervals = $this->buildIntervals($sorted);
        $cycle = $this->detectCycle($intervals);

        if ($cycle === null) {
            return null;
        }

        $amounts = $sorted->pluck('amount')->map(fn ($a) => (float) $a);

        $confidence = min(100, max(0,
            $keywordScore
            + $this->scoreAmounts($amounts)
            + $this->scoreIntervals($intervals, $cycle)
            + $this->scoreCount($count)
            + $this->scoreOvercharge($sorted, $cycle)
        ));

        if ($confidence < self::AUTO_CREATE_THRESHOLD) {
            return null;
        }

        $amountMode = $amounts
            ->groupBy(fn ($a) => (string) $a)
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->keys()
            ->first() ?? (string) $amounts->average();

        return [
            'cycle' => $cycle,
            'confidence' => $confidence,
            'amount' => (float) $amountMode,
            'lastDate' => $sorted->last()->transaction_date,
        ];
    }

    private function buildIntervals(Collection $sorted): array
    {
        $intervals = [];
        for ($i = 1; $i < $sorted->count(); $i++) {
            $prev = Carbon::parse($sorted[$i - 1]->transaction_date);
            $curr = Carbon::parse($sorted[$i]->transaction_date);
            $intervals[] = $prev->diffInDays($curr);
        }

        return $intervals;
    }

    private function detectCycle(array $intervals): ?string
    {
        if (empty($intervals)) {
            return null;
        }

        $avg = array_sum($intervals) / \count($intervals);

        foreach (self::CYCLES as $cycleName => $spec) {
            if ($avg >= $spec['min'] && $avg <= $spec['max']) {
                return $cycleName;
            }
        }

        return null;
    }

    /**
     * Score how stable the charge amounts are.
     * Uses coefficient of variation (stddev / |mean|).
     */
    private function scoreAmounts(Collection $amounts): int
    {
        if ($amounts->count() < 2) {
            return 0;
        }

        $mean = $amounts->average();
        if ($mean == 0.0) {
            return 0;
        }

        $variance = $amounts->sum(fn ($a) => ($a - $mean) ** 2) / $amounts->count();
        $cv = sqrt($variance) / abs($mean);

        if ($cv < 0.01) {
            return 25;
        }
        if ($cv < 0.05) {
            return 15;
        }
        if ($cv < 0.15) {
            return 5;
        }

        return -15;
    }

    /**
     * Score how well the intervals fit the detected billing cycle.
     * Rewards perfect regularity; penalises chaotic spacing.
     */
    private function scoreIntervals(array $intervals, string $cycle): int
    {
        if (empty($intervals)) {
            return 0;
        }

        $spec = self::CYCLES[$cycle];
        $inRange = fn ($d) => $d >= $spec['min'] && $d <= $spec['max'];

        $withinCount = \count(array_filter($intervals, $inRange));
        $ratio = $withinCount / \count($intervals);

        if ($withinCount === \count($intervals)) {
            return 25;
        }
        if ($ratio >= 0.75) {
            return 15;
        }
        if ($ratio >= 0.5) {
            return 5;
        }

        return -20;
    }

    private function scoreCount(int $count): int
    {
        $score = 0;
        if ($count >= 3) {
            $score += 20;
        }
        if ($count >= 5) {
            $score += 10;
        }

        return $score;
    }

    /**
     * Penalise groups where consecutive charges appear too close together
     * (e.g. two charges within half a billing cycle), which is a strong
     * signal of usage-based rather than subscription billing.
     */
    private function scoreOvercharge(Collection $sorted, string $cycle): int
    {
        $halfCycle = self::CYCLES[$cycle]['days'] / 2;
        $dates = $sorted->pluck('transaction_date')->map(fn ($d) => Carbon::parse($d))->values();

        for ($i = 1; $i < $dates->count(); $i++) {
            if ($dates[$i - 1]->diffInDays($dates[$i]) < $halfCycle) {
                return -20;
            }
        }

        return 0;
    }

    /**
     * Reject groups where more than 2 charges fall within any 7-day window.
     * Genuine subscriptions charge at most once per cycle; taxi / food
     * delivery / retail patterns produce clusters of rapid charges.
     */
    private function hasTooFrequentCharges(Collection $sorted): bool
    {
        $dates = $sorted->pluck('transaction_date')
            ->map(fn ($d) => Carbon::parse($d))
            ->values();

        for ($i = 0; $i < $dates->count(); $i++) {
            $windowEnd = $dates[$i]->copy()->addDays(7);
            $inWindow = $dates->filter(fn ($d) => ! $d->lt($dates[$i]) && ! $d->gt($windowEnd))->count();

            if ($inWindow > 2) {
                return true;
            }
        }

        return false;
    }
}
