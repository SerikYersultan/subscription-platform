<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Subscription;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index()
{
    $subscriptions = Subscription::where('user_id', auth()->id())->get();
    $alerts = Alert::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    $totalMonthlySpend = $subscriptions->sum('amount');
    $activeSubscriptionsCount = $subscriptions->where('status', 'active')->count();
    
    // Исправлено: убрал лишний символ >
    $upcomingCharges = Subscription::where('user_id', auth()->id())
        ->whereDate('next_charge_date', '<=', now()->addDays(3)->toDateString())
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
