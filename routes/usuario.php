<?php

use App\Http\Controllers\Usuario\DashboardController;
use App\Http\Controllers\Usuario\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/Userdashboard', [DashboardController::class, 'index']);
});

Route::controller(TicketController::class)->group(function () {
    Route::get('/Userticket', 'index');
    Route::get('/Usertickets', 'traer');
    Route::post('/Usertickets', 'store');
    Route::put('/Usertickets/{ticket}', 'update');
    Route::delete('/Usertickets/{ticket}/eliminar', 'eliminar');
});