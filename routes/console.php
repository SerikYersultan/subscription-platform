<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\AlertService;

Artisan::command('alerts:check', function (AlertService $alertService) {
    $alertService->checkAllSubscriptions();
    $this->info('Alerts checked successfully.');
})->purpose('Check subscriptions and create alerts');

Schedule::command('alerts:check')->daily();