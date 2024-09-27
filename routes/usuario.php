<?php

use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\PabellonController;
use App\Http\Controllers\Admin\PrioridadController;
use App\Http\Controllers\Usuario\UsuarioDashboardController;
use App\Http\Controllers\Usuario\UsuarioTicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Usuario'])->group(function () {
    Route::controller(UsuarioDashboardController::class)->group(function () {
        Route::get('/user-dashboard', 'index')->name('user-dashboard');
    });

    Route::controller(UsuarioTicketController::class)->group(function () {
        Route::get('/user-ticket', 'index')->name('user-ticket');
        Route::get('/user-tickets', 'traer');
        Route::get('/user-tickets/create', 'create')->name('create-tickets');
        Route::post('/user-tickets/store', 'store');
        Route::put('/user-tickets/{user-ticket}', 'update');
        Route::delete('/user-tickets/{user-ticket}/eliminar', 'eliminar');
    });

    Route::controller(PrioridadController::class)->group(function () {
        Route::get('/prioridades', 'traer');
    });

    Route::controller(CategoriaController::class)->group(function () {
        Route::get('/categorias', 'traer');
    });

    Route::controller(PabellonController::class)->group(function () {
        Route::get('/pabellones', 'traer');
    });

    Route::controller(AulaController::class)->group(function () {
        Route::get('/aulas', 'traer');
    });
});
