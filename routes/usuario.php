<?php

use App\Http\Controllers\Usuario\UsuarioDashboardController;
use App\Http\Controllers\Usuario\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/user-dashboard', [UsuarioDashboardController::class, 'index'])->name('user-dashboard');
});