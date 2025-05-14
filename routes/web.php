<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CampaignAdjustmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RolePermissionController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('donors', DonorController::class);
    Route::resource('funds', FundController::class);
    Route::get('incomes/create', [TransactionController::class, 'createIncome'])->name('incomes.create');
    Route::post('incomes', [TransactionController::class, 'storeIncome'])->name('incomes.store');
    Route::get('expenses/create', [TransactionController::class, 'createExpense'])->name('expenses.create');
    Route::post('expenses', [TransactionController::class, 'storeExpense'])->name('expenses.store');
    Route::resource('transactions', TransactionController::class);
    Route::resource('adjustments', CampaignAdjustmentController::class);
    Route::resource('roles-permissions', RolePermissionController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
