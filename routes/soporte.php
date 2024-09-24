<?php

use App\Http\Controllers\Soporte\DashboardController;
use App\Http\Controllers\Soporte\CategoriaController;
use App\Http\Controllers\Soporte\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/supportdashboard', [DashboardController::class, 'index']);
});

Route::controller(CategoriaController::class)->group(function () {
    Route::get('/supportcategoria', 'index');
    Route::get('/supportcategorias', 'traer');
    Route::post('/supportcategorias', 'store');
    Route::put('/supportcategorias/{categoria}', 'update');
    Route::delete('/supportcategorias/{categoria}/eliminar', 'eliminar');
});

Route::controller(TicketController::class)->group(function () {
    Route::get('/supportticket', 'index');
    Route::get('/supporttickets', 'traer');
    Route::post('/supporttickets', 'store');
    Route::put('/supporttickets/{ticket}', 'update');
    Route::delete('/supporttickets/{ticket}/eliminar', 'eliminar');
});