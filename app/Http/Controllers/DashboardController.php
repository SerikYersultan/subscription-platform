<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Merchant;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Services\SubscriptionDetectorService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $subscriptions = Subscription::where('user_id', $userId)
            ->orderBy('name')
            ->get();

        $detected = $subscriptions->where('confidence_score', '>', 0)->values();

        $alerts = Alert::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $unreadAlertsCount = Alert::where('user_id', $userId)
            ->where('status', 'new')
            ->count();

        $merchants = Merchant::where('user_id', $userId)->orderBy('name')->get();

        $transactions = Transaction::where('user_id', $userId)
            ->orderByDesc('transaction_date')
            ->limit(500)
            ->get();

        $transactionCount = Transaction::where('user_id', $userId)->count();

        $upcomingCharges = Subscription::where('user_id', $userId)
            ->whereNotNull('next_billing_date')
            ->whereDate('next_billing_date', '>=', now()->toDateString())
            ->whereDate('next_billing_date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('next_billing_date')
            ->get();

        $monthlyBurn = $subscriptions->sum(function ($sub) {
            $amount = abs((float) $sub->amount);
            return match ($sub->billing_cycle) {
                'weekly'    => $amount * 52 / 12,
                'quarterly' => $amount / 3,
                'yearly'    => $amount / 12,
                default     => $amount,
            };
        });

        $recentImports = Transaction::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as import_date, COUNT(*) as total')
            ->groupBy('import_date')
            ->orderByDesc('import_date')
            ->limit(5)
            ->get();

        return view('welcome', compact(
            'subscriptions',
            'detected',
            'alerts',
            'unreadAlertsCount',
            'merchants',
            'transactions',
            'transactionCount',
            'upcomingCharges',
            'monthlyBurn',
            'recentImports',
        ));
    }

    public function detect(): RedirectResponse
    {
        $userId = auth()->id();
        $service = new SubscriptionDetectorService;
        $detected = $service->detect($userId);

        $count = $detected->count();

        return redirect()->route('dashboard')
            ->with('open_page', 'detected')
            ->with('success', "Detection complete. Found {$count} subscription(s).");
    }
}
