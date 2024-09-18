<?php

use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\PrioridadController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\PabellonController;
use App\Http\Controllers\Admin\TicketController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');

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
        Route::get('/categorias', 'traer');
        Route::post('/categorias', 'store');
        Route::put('/categorias/{categoria}', 'update');
        Route::delete('/categorias/{categoria}/eliminar', 'eliminar');
    });

    Route::controller(PabellonController::class)->group(function () {
        Route::get('/pabellon', 'index')->name('pabellon');
        Route::get('/pabellones', 'traer');
        Route::post('/pabellones', 'store');
        Route::put('/pabellones/{pabellon}', 'update');
        Route::delete('/pabellones/{pabellon}/eliminar', 'eliminar');
    });

    Route::controller(AulaController::class)->group(function () {
        Route::get('/aula', 'index')->name('aula');
        Route::get('/aulas', 'traer');
        Route::post('/aulas', 'store');
        Route::put('/aulas/{aula}', 'update');
        Route::delete('/aulas/{aula}/eliminar', 'eliminar');
    });

    Route::controller(TicketController::class)->group(function () {
        Route::get('/ticket', 'index')->name('ticket');
        Route::get('/tickets', 'traer');
        Route::post('/tickets', 'store');
    });

    Route::controller(PrioridadController::class)->group(function () {
        Route::get('/prioridades', 'traer');
    });
});
