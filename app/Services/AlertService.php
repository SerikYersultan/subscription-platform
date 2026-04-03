<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Subscription;
use Carbon\Carbon;

class AlertService
{
    public function createPriceIncreaseAlert(Subscription $subscription, float $oldAmount, float $newAmount): void
    {
        if ($newAmount <= $oldAmount) {
            return;
        }

        $exists = Alert::where('user_id', $subscription->user_id)
            ->where('subscription_id', $subscription->id)
            ->where('type', 'price_increase')
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if (!$exists) {
            Alert::create([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'type' => 'price_increase',
                'message' => "Price increased for {$subscription->name}: {$oldAmount} -> {$newAmount}",
                'status' => 'new',
            ]);
        }
    }

public function createUpcomingChargeAlert(Subscription $subscription, int $daysBefore = 3): void
{
    if (!$subscription->next_charge_date) {
        return;
    }

    $chargeDate = \Carbon\Carbon::parse($subscription->next_charge_date)->startOfDay();
    $today = \Carbon\Carbon::now()->startOfDay();
    
    // Проверяем, что дата списания между сегодня и сегодня+daysBefore
    if ($chargeDate->lt($today) || $chargeDate->gt($today->copy()->addDays($daysBefore))) {
        return;
    }

    // Проверяем, нет ли уже алерта для этой подписки
    $exists = Alert::where('user_id', $subscription->user_id)
        ->where('subscription_id', $subscription->id)
        ->where('type', 'upcoming_charge')
        ->exists();

    if (!$exists) {
        Alert::create([
            'user_id' => $subscription->user_id,
            'subscription_id' => $subscription->id,
            'type' => 'upcoming_charge',
            'message' => "Upcoming charge for {$subscription->merchant_name} on {$chargeDate->format('Y-m-d')}",
            'status' => 'new',
        ]);
        
        // Вывод в консоль для проверки
        echo "Alert created for {$subscription->merchant_name}\n";
    }
}
    public function checkAllSubscriptions(): void
    {
        $subscriptions = Subscription::all();

        foreach ($subscriptions as $subscription) {
            $this->createUpcomingChargeAlert($subscription);
        }
    }
}