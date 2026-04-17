<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SubscriptionDetectorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionDetectorTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private SubscriptionDetectorService $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->detector = new SubscriptionDetectorService;
    }

    private function makeTransactions(string $merchant, array $dates, float $amount, ?string $description = null): void
    {
        foreach ($dates as $date) {
            Transaction::create([
                'user_id' => $this->user->id,
                'merchant_name' => $merchant,
                'description' => $description,
                'amount' => $amount,
                'currency' => 'KZT',
                'transaction_date' => $date,
            ]);
        }
    }

    // ── Positive cases: should be detected ────────────────────────────────────

    public function test_yandex_plus_monthly_is_detected(): void
    {
        $this->makeTransactions('Yandex.Plus', ['2024-01-15', '2024-02-15', '2024-03-15'], -899);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(1, $result);
        $this->assertEquals('monthly', $result->first()->billing_cycle);
    }

    public function test_yandex_music_monthly_is_detected(): void
    {
        $this->makeTransactions('Yandex.Music', ['2024-01-10', '2024-02-10', '2024-03-10'], -399);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(1, $result);
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'billing_cycle' => 'monthly',
        ]);
    }

    public function test_kinopoisk_monthly_is_detected(): void
    {
        $this->makeTransactions('Kinopoisk', ['2024-01-05', '2024-02-05', '2024-03-05'], -599);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(1, $result);
    }

    public function test_netflix_monthly_is_detected(): void
    {
        $this->makeTransactions('Netflix', ['2024-01-01', '2024-02-01', '2024-03-01'], -2490, 'Purchases');

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(1, $result);
        $this->assertEquals('monthly', $result->first()->billing_cycle);
    }

    public function test_spotify_with_slight_date_drift_is_detected(): void
    {
        // Real monthly billing sometimes drifts a day or two
        $this->makeTransactions('Spotify', ['2024-01-14', '2024-02-15', '2024-03-13', '2024-04-14'], -319);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(1, $result);
    }

    public function test_unnamed_service_with_stable_monthly_charges_is_detected(): void
    {
        // No subscription keyword — passes purely on pattern regularity
        $this->makeTransactions('SportLife', ['2024-01-01', '2024-02-01', '2024-03-01'], -5000);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(1, $result);
    }

    public function test_yearly_subscription_is_detected(): void
    {
        $this->makeTransactions('Adobe Creative Cloud', [
            '2022-03-01', '2023-03-01', '2024-03-01',
        ], -12000, 'subscription');

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(1, $result);
        $this->assertEquals('yearly', $result->first()->billing_cycle);
    }

    public function test_confidence_score_is_stored(): void
    {
        $this->makeTransactions('Netflix', ['2024-01-01', '2024-02-01', '2024-03-01'], -2490);

        $this->detector->detect($this->user->id);

        $sub = Subscription::where('user_id', $this->user->id)->first();
        $this->assertNotNull($sub);
        $this->assertGreaterThanOrEqual(70, $sub->confidence_score);
    }

    // ── Negative cases: should NOT be detected ─────────────────────────────────

    public function test_yandex_go_rides_are_not_detected(): void
    {
        // Taxi rides — varying amounts, frequent, negative keyword
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'YANDEX.GO', 'amount' => -1200, 'currency' => 'KZT', 'transaction_date' => '2024-01-02', 'description' => 'Purchases']);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'YANDEX.GO', 'amount' => -870,  'currency' => 'KZT', 'transaction_date' => '2024-01-05', 'description' => 'Purchases']);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'YANDEX.GO', 'amount' => -1540, 'currency' => 'KZT', 'transaction_date' => '2024-01-08', 'description' => 'Purchases']);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'YANDEX.GO', 'amount' => -990,  'currency' => 'KZT', 'transaction_date' => '2024-01-11', 'description' => 'Purchases']);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(0, $result);
    }

    public function test_yandex_eats_deliveries_are_not_detected(): void
    {
        $this->makeTransactions('Yandex.Eats', ['2024-01-03', '2024-02-03', '2024-03-03'], -2500);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(0, $result);
    }

    public function test_yandex_lavka_is_not_detected(): void
    {
        $this->makeTransactions('YANDEX.LAVKA', ['2024-01-10', '2024-02-10', '2024-03-10'], -3100);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(0, $result);
    }

    public function test_grocery_store_is_not_detected(): void
    {
        // Grocery — varying amounts, no subscription signal
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'Magnum Market', 'amount' => -4500, 'currency' => 'KZT', 'transaction_date' => '2024-01-07', 'description' => 'Purchases']);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'Magnum Market', 'amount' => -8200, 'currency' => 'KZT', 'transaction_date' => '2024-02-07', 'description' => 'Purchases']);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'Magnum Market', 'amount' => -3100, 'currency' => 'KZT', 'transaction_date' => '2024-03-07', 'description' => 'Purchases']);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(0, $result);
    }

    public function test_taxi_service_is_not_detected(): void
    {
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'City Taxi', 'amount' => -1500, 'currency' => 'KZT', 'transaction_date' => '2024-01-03', 'description' => 'taxi']);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'City Taxi', 'amount' => -2100, 'currency' => 'KZT', 'transaction_date' => '2024-02-03', 'description' => 'taxi']);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'City Taxi', 'amount' => -1800, 'currency' => 'KZT', 'transaction_date' => '2024-03-03', 'description' => 'taxi']);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(0, $result);
    }

    public function test_too_few_transactions_are_not_detected(): void
    {
        // Only 2 — below MIN_TRANSACTIONS of 3
        $this->makeTransactions('Netflix', ['2024-01-01', '2024-02-01'], -2490);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(0, $result);
    }

    public function test_chaotic_intervals_are_not_detected(): void
    {
        // Random spacing — not a real subscription pattern
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'SomeApp', 'amount' => -500, 'currency' => 'KZT', 'transaction_date' => '2024-01-01', 'description' => null]);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'SomeApp', 'amount' => -500, 'currency' => 'KZT', 'transaction_date' => '2024-01-08', 'description' => null]);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'SomeApp', 'amount' => -500, 'currency' => 'KZT', 'transaction_date' => '2024-02-20', 'description' => null]);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(0, $result);
    }

    public function test_highly_variable_amounts_are_not_detected(): void
    {
        // Same merchant, same monthly interval, but wildly different amounts
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'SomeShop', 'amount' => -500,   'currency' => 'KZT', 'transaction_date' => '2024-01-15', 'description' => null]);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'SomeShop', 'amount' => -12000, 'currency' => 'KZT', 'transaction_date' => '2024-02-15', 'description' => null]);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'SomeShop', 'amount' => -3400,  'currency' => 'KZT', 'transaction_date' => '2024-03-15', 'description' => null]);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(0, $result);
    }

    public function test_too_frequent_charges_are_not_detected(): void
    {
        // 3 charges within 7 days → rejected as non-subscription
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'FastFood', 'amount' => -1500, 'currency' => 'KZT', 'transaction_date' => '2024-01-01', 'description' => null]);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'FastFood', 'amount' => -1500, 'currency' => 'KZT', 'transaction_date' => '2024-01-03', 'description' => null]);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'FastFood', 'amount' => -1500, 'currency' => 'KZT', 'transaction_date' => '2024-01-05', 'description' => null]);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(0, $result);
    }

    // ── Isolation: different services under same brand are independent ─────────

    public function test_yandex_plus_and_yandex_go_are_independent(): void
    {
        // Plus → subscription; Go → not
        $this->makeTransactions('Yandex.Plus', ['2024-01-15', '2024-02-15', '2024-03-15'], -899);

        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'YANDEX.GO', 'amount' => -800,  'currency' => 'KZT', 'transaction_date' => '2024-01-03', 'description' => 'Purchases']);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'YANDEX.GO', 'amount' => -1100, 'currency' => 'KZT', 'transaction_date' => '2024-01-06', 'description' => 'Purchases']);
        Transaction::create(['user_id' => $this->user->id, 'merchant_name' => 'YANDEX.GO', 'amount' => -950,  'currency' => 'KZT', 'transaction_date' => '2024-01-09', 'description' => 'Purchases']);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(1, $result);
        $this->assertStringContainsStringIgnoringCase('plus', strtolower($result->first()->name));
    }

    public function test_updateOrCreate_does_not_duplicate_subscriptions(): void
    {
        $this->makeTransactions('Netflix', ['2024-01-01', '2024-02-01', '2024-03-01'], -2490);

        $this->detector->detect($this->user->id);
        $this->detector->detect($this->user->id);

        $this->assertCount(1, Subscription::where('user_id', $this->user->id)->get());
    }

    // ── Multi-price-point (APPLE.COM/BILL style) ───────────────────────────────

    public function test_same_merchant_different_prices_create_separate_subscriptions(): void
    {
        // iCloud 50GB — 449 KZT/month
        $this->makeTransactions('APPLE.COM/BILL', ['2024-01-07', '2024-02-07', '2024-03-07'], -449);
        // Apple Music — 899 KZT/month
        $this->makeTransactions('APPLE.COM/BILL', ['2024-01-07', '2024-02-07', '2024-03-07'], -899);
        // Apple TV+ — 349 KZT/month
        $this->makeTransactions('APPLE.COM/BILL', ['2024-01-07', '2024-02-07', '2024-03-07'], -349);

        $result = $this->detector->detect($this->user->id);

        $this->assertCount(3, $result);

        $amounts = Subscription::where('user_id', $this->user->id)
            ->pluck('amount')
            ->map(fn ($a) => abs((float) $a))
            ->sort()
            ->values()
            ->all();

        $this->assertEquals([349.0, 449.0, 899.0], $amounts);
    }

    public function test_multi_price_subscriptions_have_disambiguated_names(): void
    {
        $this->makeTransactions('APPLE.COM/BILL', ['2024-01-07', '2024-02-07', '2024-03-07'], -449);
        $this->makeTransactions('APPLE.COM/BILL', ['2024-01-07', '2024-02-07', '2024-03-07'], -899);

        $this->detector->detect($this->user->id);

        $names = Subscription::where('user_id', $this->user->id)->pluck('name')->all();

        foreach ($names as $name) {
            $this->assertStringContainsString('apple.com/bill', $name);
            // Each name must include the amount to tell the two apart
            $this->assertMatchesRegularExpression('/\d+\.\d{2}/', $name);
        }
    }

    public function test_single_price_point_subscription_name_has_no_amount_suffix(): void
    {
        $this->makeTransactions('Netflix', ['2024-01-01', '2024-02-01', '2024-03-01'], -2490);

        $this->detector->detect($this->user->id);

        $name = Subscription::where('user_id', $this->user->id)->value('name');

        $this->assertEquals('netflix', $name);
    }

    public function test_same_merchant_same_price_does_not_duplicate_on_rescan(): void
    {
        $this->makeTransactions('APPLE.COM/BILL', ['2024-01-07', '2024-02-07', '2024-03-07'], -449);
        $this->makeTransactions('APPLE.COM/BILL', ['2024-01-07', '2024-02-07', '2024-03-07'], -899);

        $this->detector->detect($this->user->id);
        $this->detector->detect($this->user->id);

        $this->assertCount(2, Subscription::where('user_id', $this->user->id)->get());
    }

    public function test_mixed_price_only_stable_prices_are_detected(): void
    {
        // 899 KZT consistently each month → subscription
        $this->makeTransactions('APPLE.COM/BILL', ['2024-01-07', '2024-02-07', '2024-03-07'], -899);

        // One-off charge at 12000 — only 1 transaction, below MIN_TRANSACTIONS
        Transaction::create([
            'user_id' => $this->user->id,
            'merchant_name' => 'APPLE.COM/BILL',
            'amount' => -12000,
            'currency' => 'KZT',
            'transaction_date' => '2024-02-15',
            'description' => 'Purchases',
        ]);

        $result = $this->detector->detect($this->user->id);

        // Only the recurring 899 tier should be detected
        $this->assertCount(1, $result);
        $this->assertEquals(899.0, abs((float) $result->first()->amount));
    }
}
