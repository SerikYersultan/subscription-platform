<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\AlertService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
public function index(): View
{
    $subscriptions = Subscription::where('user_id', auth()->id())
        ->orderBy('merchant_name')  // ✅ ИСПРАВЛЕНО
        ->get();

    return view('subscriptions.index', compact('subscriptions'));
}

    public function update(Request $request, Subscription $subscription, AlertService $alertService)
{
    // Проверяем, что подписка принадлежит пользователю
    if ($subscription->user_id !== auth()->id()) {
        abort(403);
    }

    // Сохраняем старую цену
    $oldAmount = $subscription->amount;

    // Валидация
    $validated = $request->validate([
        'merchant_name' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'status' => 'required|string',
        'next_charge_date' => 'nullable|date',
    ]);

    // Обновляем
    $subscription->update($validated);

    // Проверяем изменение цены
    $alertService->createPriceIncreaseAlert($subscription, $oldAmount, $subscription->amount);

    return redirect()->route('subscriptions.index')
        ->with('success', 'Subscription updated successfully.');
}
}