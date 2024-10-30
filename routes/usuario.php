<?php

use App\Http\Controllers\Usuario\UsuarioComentarioController;
use App\Http\Controllers\Usuario\UsuarioDashboardController;
use App\Http\Controllers\Usuario\UsuarioTicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Usuario'])->group(function () {
    Route::controller(UsuarioDashboardController::class)->group(function () {
        Route::get('/user-dashboard/fetch-tickets-data', 'traer');
    });

    Route::controller(UsuarioTicketController::class)->group(function () {
        Route::get('/user-ticket', 'index')->name('user-ticket');
        Route::get('/user-tickets', 'traer');
        Route::get('/user-tickets', 'traerReabiertos');
        Route::get('/user-tickets/create', 'create')->name('create-tickets');
        Route::post('/user-tickets/store', 'store');
        Route::put('/user-tickets/{ticket}', 'update');
        Route::post('/user-tickets/{ticket}/upload', 'upload');
        Route::delete('/user-tickets/{ticket}/eliminar', 'eliminar');
    });

    Route::controller(UsuarioComentarioController::class)->group(function () {
        Route::get('/user-comentario/{ticket}', 'verComentarios')->name('usuario.comentario.ver');
        Route::post('/user-tickets/{ticket}/comentarios', 'guardarComentario')->name('usuario.comentarios.guardar');
    });
});
