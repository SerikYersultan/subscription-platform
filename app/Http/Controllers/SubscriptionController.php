<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\AlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(): View
    {
        $subscriptions = Subscription::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('subscriptions.index', compact('subscriptions'));
    }

    public function create(): View
    {
        return view('subscriptions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'amount'            => 'required|numeric|min:0',
            'currency'          => 'required|string|size:3',
            'billing_cycle'     => 'required|in:weekly,monthly,quarterly,yearly',
            'status'            => 'required|in:active,cancelled,paused',
            'next_billing_date' => 'nullable|date',
        ]);

        Subscription::create([
            'user_id'         => auth()->id(),
            'confidence_score' => 0,
            ...$validated,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Subscription added.');
    }

    public function confirm(Subscription $subscription): RedirectResponse
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }

        $subscription->update(['confidence_score' => 0]);

        return redirect()->route('dashboard')
            ->with('open_page', 'detected')
            ->with('success', "Confirmed \"{$subscription->name}\".");
    }

    public function update(Request $request, Subscription $subscription, AlertService $alertService): RedirectResponse
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }

        $oldAmount = (float) $subscription->amount;

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'currency'      => 'required|string|size:3',
            'billing_cycle' => 'required|in:weekly,monthly,quarterly,yearly',
        ]);

        $subscription->update([
            ...$validated,
            'confidence_score' => 0,
        ]);

        $newAmount = (float) $validated['amount'];
        $alertService->createPriceIncreaseAlert($subscription, $oldAmount, $newAmount);

        return redirect()->route('dashboard')
            ->with('open_page', 'detected')
            ->with('success', "Updated \"{$subscription->name}\".");
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }

        $subscription->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Subscription removed.');
    }

    public function show(Subscription $subscription): View
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }

        return view('subscriptions.show', compact('subscription'));
    }
}
