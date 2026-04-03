<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(): View
    {
        $alerts = Alert::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('alerts.index', compact('alerts'));
    }
}