<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Redirect home to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Protected routes (Only for logged-in users)
Route::middleware('auth')->group(function () {

    // Profile (только стандартные роуты Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Import CSV
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Merchants
    Route::get('/merchants', [MerchantController::class, 'index'])->name('merchants.index');

    // Run subscription detector (re-scan)
    Route::post('/detect', [DashboardController::class, 'detect'])->name('detect');

    // Subscription confirmation & editing
    Route::post('/subscriptions/{subscription}/confirm', [SubscriptionController::class, 'confirm'])->name('subscriptions.confirm');
    Route::patch('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
});

require __DIR__.'/auth.php';
