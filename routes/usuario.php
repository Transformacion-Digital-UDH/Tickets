<?php

use App\Http\Controllers\Usuario\UsuarioDashboardController;
use App\Http\Controllers\Usuario\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::controller(UsuarioDashboardController::class)->group(function () {
        Route::get('/user-dashboard', 'index')->name('user-dashboard');
    });
});