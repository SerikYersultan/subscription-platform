<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MerchantController;
use Illuminate\Support\Facades\Route;

// Redirect home to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
});

require __DIR__.'/auth.php';