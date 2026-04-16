<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function confirm(Subscription $subscription)
    {
        abort_if($subscription->user_id !== Auth::id(), 403);

        $subscription->update(['confidence_score' => 100]);

        return redirect()->route('dashboard')
            ->with('open_page', 'detected')
            ->with('success', '"'.$subscription->name.'" confirmed as a real subscription.');
    }

    public function update(Request $request, Subscription $subscription)
    {
        abort_if($subscription->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0|max:9999999',
            'billing_cycle' => 'required|in:weekly,monthly,quarterly,yearly',
            'currency'      => 'required|string|size:3',
        ]);

        $subscription->update(array_merge($data, ['confidence_score' => 100]));

        return redirect()->route('dashboard')
            ->with('open_page', 'subscriptions')
            ->with('success', '"'.$subscription->name.'" saved and confirmed.');
    }

    public function destroy(Subscription $subscription)
    {
        abort_if($subscription->user_id !== Auth::id(), 403);

        $name = $subscription->name;
        $subscription->delete();

        return redirect()->route('dashboard')
            ->with('open_page', 'detected')
            ->with('success', '"'.$name.'" removed.');
    }
}
