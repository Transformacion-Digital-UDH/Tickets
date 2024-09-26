<?php

use App\Http\Controllers\Soporte\SoporteDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::controller(SoporteDashboardController::class)->group(function () {
        Route::get('/support-dashboard', 'index')->name('support-dashboard');
    });
});