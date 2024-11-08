<?php

use App\Http\Controllers\Soporte\HistorialController;
use App\Http\Controllers\Soporte\SoporteComentarioController;
use App\Http\Controllers\Soporte\SoporteDashboardController;
use App\Http\Controllers\Soporte\SoporteTicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Soporte'])->group(function () {
    Route::controller(SoporteDashboardController::class)->group(function () {
    });

    Route::controller(SoporteTicketController::class)->group(function () {
        Route::get('/support-ticket', 'index')->name('support-ticket');

        Route::get('/support-optener', 'obtenerTickets')->name('support-optener');
        Route::post('/soporte/tickets/finalizar/{id}',  'finalizarTicket')->name('support-finalizar');

        //Ruta para aceptar el ticket
        Route::put('/soporte/tickets/aceptar/{id}', 'aceptarTicket')->name('support-aceptar');
    });
    Route::controller(HistorialController::class)->group(function () {
        Route::get('/support-historial', 'index')->name('support-historial');
    });

    Route::controller(SoporteComentarioController::class)->group(function () {
        Route::get('/soporte/comentario/{ticket}', 'verComentarios')->name('soporte.comentario.ver');
        Route::post('/soporte/tickets/{ticket}/comentarios', 'guardarComentario')->name('soporte.comentarios.guardar');
    });
});
