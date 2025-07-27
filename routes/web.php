<?php

use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CampaignAdjustmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/donors/dropdown', [DonorController::class, 'getDropdown']);
    Route::resource('donors', DonorController::class);

    Route::get('/funds/{fund}/history', [FundController::class, 'history'])->name('funds.history');
    Route::get('/funds/dropdown', [FundController::class, 'getDropdown']);
    Route::resource('funds', FundController::class);
    Route::get('incomes/create', [TransactionController::class, 'createIncome'])->name('incomes.create');
    Route::post('incomes', [TransactionController::class, 'storeIncome'])->name('incomes.store');
    Route::get('expenses/create', [TransactionController::class, 'createExpense'])->name('expenses.create');
    Route::post('expenses', [TransactionController::class, 'storeExpense'])->name('expenses.store');
    Route::resource('transactions', TransactionController::class);
    Route::resource('adjustments', CampaignAdjustmentController::class);
    Route::resource('users', UserController::class);
    Route::get('roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');
    Route::post('roles-permissions', [RolePermissionController::class, 'store'])->name('roles-permissions.store');
    Route::get('roles-permissions/create', [RolePermissionController::class, 'create'])->name('roles-permissions.create');
    Route::get('roles-permissions/{role}', [RolePermissionController::class, 'show'])->name('roles-permissions.show');
    Route::get('roles-permissions/{role}/edit', [RolePermissionController::class, 'edit'])->name('roles-permissions.edit');
    Route::put('roles-permissions/{role}', [RolePermissionController::class, 'update'])->name('roles-permissions.update');
    Route::delete('roles-permissions/{role}', [RolePermissionController::class, 'destroy'])->name('roles-permissions.destroy');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/activity-log', [ActivityLogController::class, 'index'])
        ->name('activity-log.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
