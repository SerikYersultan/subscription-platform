<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Subscription;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $subscriptions = Subscription::where('user_id', auth()->id())->get();

        $alerts = Alert::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        $totalMonthlySpend = $subscriptions->sum('amount');
        $activeSubscriptionsCount = $subscriptions->where('status', 'active')->count();

        $upcomingCharges = Subscription::where('user_id', auth()->id())
            >whereDate('next_charge_date', '<=', now()->addDays(3)->toDateString())
            ->whereDate('next_charge_date', '>=', now()->toDateString())
            ->get();

        return view('dashboard', compact(
            'subscriptions',
            'alerts',
            'totalMonthlySpend',
            'activeSubscriptionsCount',
            'upcomingCharges'
        ));
    }
}