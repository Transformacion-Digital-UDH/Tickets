<?php

use App\Http\Controllers\Admin\AsignadoController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\PrioridadController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PabellonController;
use App\Http\Controllers\Admin\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/dashboard/fetch-tickets-data', 'traer');
    });

    Route::controller(SedeController::class)->group(function () {
        Route::get('/sede', 'index')->name('sede');
        Route::get('/elegirsede', 'elegirsede')->name('elegirsede');
        Route::get('/sedes', 'traer');
        Route::post('/sedes', 'store');
        Route::put('/sedes/{sede}', 'update');
        Route::delete('/sedes/{sede}/eliminar', 'eliminar');
    });


    Route::controller(UsuarioController::class)->group(function () {
        /** SOPORTE TÉCNICO **/
        Route::get('/soporte', 'soporte')->name('soporte');
        Route::get('/soportes', 'traerSoporte');
        Route::post('/soportes', 'storeSoporte');
        Route::put('/soportes/{soporte}', 'updateSoporte');
        Route::delete('/soportes/{soporte}/eliminar', 'eliminarSoporte');
        /** DOCENTE **/
        Route::get('/usuario', 'usuario')->name('usuario');
        Route::get('/usuarios', 'traerUsuario');
        Route::post('/usuarios', 'storeUsuario');
        Route::put('/usuarios/{usuario}', 'updateUsuario');
        Route::delete('/usuarios/{usuario}/eliminar', 'eliminarUsuario');
    });

    Route::controller(CategoriaController::class)->group(function () {
        Route::get('/categoria', 'index')->name('categoria');
        Route::post('/categorias', 'store');
        Route::put('/categorias/{categoria}', 'update');
        Route::delete('/categorias/{categoria}/eliminar', 'eliminar');
    });

    Route::controller(PabellonController::class)->group(function () {
        Route::get('/pabellon', 'index')->name('pabellon');
        Route::post('/pabellones', 'store');
        Route::put('/pabellones/{pabellon}', 'update');
        Route::delete('/pabellones/{pabellon}/eliminar', 'eliminar');
    });

    Route::controller(AulaController::class)->group(function () {
        Route::get('/aula', 'index')->name('aula');
        Route::post('/aulas', 'store');
        Route::put('/aulas/{aula}', 'update');
        Route::delete('/aulas/{aula}/eliminar', 'eliminar');
    });

    Route::controller(TicketController::class)->group(function () {
        Route::get('/ticket', 'index')->name('ticket');
        Route::get('/tickets', 'traer');
        Route::post('/tickets', 'store');
        Route::post('/tickets/{id}/asignar', 'asignarSoporte');
        Route::put('/tickets/{id}/actualizar', 'asignarSoporte');
        Route::put('/tickets/{id}', 'update');
        Route::put('/tickets/{id}/updateEstado', 'updateEstado');
        Route::post('/tickets/{id}/upload', 'upload');
        Route::delete('/tickets/{id}/eliminar', 'eliminar');
    });
});

Route::middleware(['auth', 'role:Admin|Usuario'])->group(function () {
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
