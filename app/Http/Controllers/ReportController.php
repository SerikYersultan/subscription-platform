<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;

class ReportController extends Controller
{
    // Добавим этот метод
    public function index(): View
    {
        $userId = auth()->id();
        
        $totalSubscriptions = Subscription::where('user_id', $userId)->count();
        $activeSubscriptions = Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->count();
        
        $totalMonthlySpend = Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->sum('amount');
        
        $unreadAlerts = Alert::where('user_id', $userId)
            ->where('status', 'unread')
            ->count();
        
        $recentAlerts = Alert::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();
        
        return view('reports.index', compact(
            'totalSubscriptions',
            'activeSubscriptions', 
            'totalMonthlySpend',
            'unreadAlerts',
            'recentAlerts'
        ));
    }

    public function pdf()
    {
        $subscriptions = Subscription::where('user_id', auth()->id())->get();
        $total = $subscriptions->sum('amount');

        $pdf = Pdf::loadView('reports.subscriptions', compact('subscriptions', 'total'));
        return $pdf->download('subscriptions-report.pdf');
    }

    public function alertsPdf()
    {
        $alerts = Alert::where('user_id', auth()->id())
            ->latest()
            ->get();
        
        $pdf = Pdf::loadView('reports.alerts', compact('alerts'));
        return $pdf->download('alerts-report.pdf');
    }
}