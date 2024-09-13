<?php

use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\SubcategoriaController;
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
        Route::get('/sedes', 'traer');
        Route::post('/sedes', 'store');
        Route::put('/sedes/{sede}', 'update');
        Route::delete('/sedes/{sede}/desactivar', 'desactivar');
        Route::put('/sedes/{sede}/activar', 'activar');
    });


    Route::controller(UsuarioController::class)->group(function () {
        /** SOPORTE TÉCNICO **/
        Route::get('/soporte', 'soporte')->name('soporte');
        Route::get('/soportes', 'traerSoporte');
        Route::post('/soportes', 'storeSoporte');
        Route::put('/soportes/{soporte}', 'updateSoporte');
        Route::delete('/soportes/{soporte}/desactivar', 'desactivarSoporte');
        Route::put('/soportes/{soporte}/activar', 'activarSoporte');
        /** DOCENTE **/
        Route::get('/docente', 'docente')->name('docente');
        Route::get('/docentes', 'traerDocente');
        Route::post('/docentes', 'storeDocente');
        Route::put('/docentes/{docente}', 'updateDocente');
        Route::delete('/docentes/{docente}/desactivar', 'desactivarDocente');
        Route::put('/docentes/{docente}/activar', 'activarDocente');
    });

    Route::controller(TicketController::class)->group(function () {
        Route::get('/ticket', 'index')->name('ticket');
    });

    Route::controller(PabellonController::class)->group(function () {
        Route::get('/pabellon', 'index')->name('pabellon');
        Route::get('/pabellons', 'traer');
        Route::post('/pabellons', 'store');
        Route::put('/pabellons/{pabellon}', 'update');
        Route::delete('/pabellons/{pabellon}', 'destroy');
    });

    Route::controller(CategoriaController::class)->group(function () {
        Route::get('/categoria', 'index')->name('categoria');
        Route::get('/categorias', 'traer');
        Route::post('/categorias', 'store');
        Route::put('/categorias/{categoria}', 'update');
        Route::delete('/categorias/{categoria}', 'destroy');
    });

    Route::controller(SubcategoriaController::class)->group(function () {
        Route::get('/subcategoria', 'index')->name('subcategoria');
        Route::get('/subcategorias', 'traer');
        Route::post('/subcategorias', 'store');
        Route::put('/subcategorias/{subcategoria}', 'update');
        Route::delete('/subcategorias/{subcategoria}', 'destroy');
    });

    Route::controller(AulaController::class)->group(function () {
        Route::get('/aula', 'index')->name('aula');
    });
});
