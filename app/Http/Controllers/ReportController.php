<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Alert;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
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