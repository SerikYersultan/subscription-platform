<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Merchant;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Services\SubscriptionDetectorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $subscriptions = Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('next_billing_date')
            ->get();

        $monthlyBurn = $subscriptions->sum(function ($sub) {
            return match ($sub->billing_cycle) {
                'weekly' => $sub->amount * 4.33,
                'monthly' => $sub->amount,
                'quarterly' => $sub->amount / 3,
                'yearly' => $sub->amount / 12,
                default => 0,
            };
        });

        $upcomingCharges = $subscriptions->filter(function ($sub) {
            if (! $sub->next_billing_date) {
                return false;
            }
            $days = Carbon::parse($sub->next_billing_date)->startOfDay()
                ->diffInDays(now()->startOfDay(), false);

            return $days >= 0 && $days <= 7;
        })->sortBy('next_billing_date');

        $detected = Subscription::where('user_id', $userId)
            ->where('confidence_score', '<', 80)
            ->where('status', 'active')
            ->get();

        $alerts = Alert::where('user_id', $userId)
            ->with('subscription')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $unreadAlertsCount = Alert::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        $transactionCount = Transaction::where('user_id', $userId)->count();

        $transactions = Transaction::where('user_id', $userId)
            ->orderByDesc('transaction_date')
            ->limit(500)
            ->get();

        $merchants = Merchant::where('user_id', $userId)
            ->withCount('transactions')
            ->orderBy('canonical_name')
            ->get();

        $recentImports = Transaction::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as import_date, COUNT(*) as total')
            ->groupBy('import_date')
            ->orderByDesc('import_date')
            ->limit(5)
            ->get();

        return view('welcome', compact(
            'subscriptions',
            'monthlyBurn',
            'upcomingCharges',
            'detected',
            'alerts',
            'unreadAlertsCount',
            'transactions',
            'transactionCount',
            'merchants',
            'recentImports'
        ));
    }

    public function detect()
    {
        $userId = Auth::id();
        $found = (new SubscriptionDetectorService)->detect($userId);

        return redirect()->route('dashboard')
            ->with('success', "Detection complete. Found {$found->count()} subscription(s).");
    }
}
