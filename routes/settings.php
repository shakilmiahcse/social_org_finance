<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\FundTypeController;
use App\Http\Controllers\Settings\OrgSettingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');


    Route::get('settings/org', [OrgSettingController::class, 'edit'])->name('org.edit');
    Route::put('/settings/org', [OrgSettingController::class, 'update'])->name('org.update');

    Route::get('settings/fund-type', [FundTypeController::class, 'edit'])->name('fund-type.edit');
    Route::put('settings/fund-type', [FundTypeController::class, 'update'])->name('fund-type.update');
});
