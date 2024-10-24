<?php

use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ComentarioController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PabellonController;
use App\Http\Controllers\Admin\PrioridadController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Soporte\SoporteDashboardController;
use App\Http\Controllers\Usuario\UsuarioDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard/fetch-tickets-data', 'traer');
    });

    Route::controller(SedeController::class)->group(function () {
        Route::get('/sede', 'index')->name('sede');
        Route::get('/sedespaginated', 'traerPaginated');
        Route::post('/sedes', 'store');
        Route::post('/sedes/{id}/upload', 'upload');
        Route::put('/sedes/{sede}', 'update');
        Route::delete('/sedes/{sede}/eliminar', 'eliminar');
    });

    Route::controller(UsuarioController::class)->group(function () {
        /** SOPORTE TÉCNICO **/
        Route::get('/soporte', 'soporte')->name('soporte');
        Route::get('/soportes', 'traerSoporte');
        Route::get('/soportespaginated', 'traerSoportePaginated');
        Route::post('/soportes', 'storeSoporte');
        Route::put('/soportes/{soporte}', 'updateSoporte');
        Route::delete('/soportes/{soporte}/eliminar', 'eliminarSoporte');
        /** DOCENTE **/
        Route::get('/usuario', 'usuario')->name('usuario');
        Route::get('/usuariospaginated', 'traerUsuarioPaginated');
        Route::get('/usuarios', 'traerUsuario');
        Route::post('/usuarios', 'storeUsuario');
        Route::put('/usuarios/{usuario}', 'updateUsuario');
        Route::delete('/usuarios/{usuario}/eliminar', 'eliminarUsuario');
    });

    Route::controller(CategoriaController::class)->group(function () {
        Route::get('/categoria', 'index')->name('categoria');
        Route::get('/categoriaspaginated', 'traerPaginated');
        Route::post('/categorias', 'store');
        Route::put('/categorias/{categoria}', 'update');
        Route::delete('/categorias/{categoria}/eliminar', 'eliminar');
    });

    Route::controller(PabellonController::class)->group(function () {
        Route::get('/pabellon', 'index')->name('pabellon');
        Route::get('/pabellonespaginated', 'traerPaginated');
        Route::post('/pabellones', 'store');
        Route::put('/pabellones/{pabellon}', 'update');
        Route::delete('/pabellones/{pabellon}/eliminar', 'eliminar');
    });

    Route::controller(AulaController::class)->group(function () {
        Route::get('/aula', 'index')->name('aula');
        Route::get('/aulaspaginated', 'traerPaginated');
        Route::post('/aulas', 'store');
        Route::put('/aulas/{aula}', 'update');
        Route::delete('/aulas/{aula}/eliminar', 'eliminar');
    });

    Route::controller(TicketController::class)->group(function () {
        Route::get('/ticket', 'index')->name('ticket');
        Route::get('/ticketspaginated', 'traerPaginated');
        Route::get('/tickets', 'traer');
        Route::post('/tickets', 'store');
        Route::post('/tickets/{id}/asignar', 'asignarSoporte');
        Route::put('/tickets/{id}/actualizar', 'asignarSoporte');
        Route::put('/tickets/{id}', 'update');
        Route::put('/tickets/{id}/updateEstado', 'updateEstado');
        Route::post('/tickets/{id}/upload', 'upload');
        Route::delete('/tickets/{id}/eliminar', 'eliminar');
    });

    Route::controller(ComentarioController::class)->group(function () {
        Route::get('/comentario/{ticket}', 'verComentarios')->name('comentario.ver');
        Route::post('/tickets/{ticket}/comentarios', 'guardarComentario')->name('comentarios.guardar');
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

Route::middleware(['auth', 'role:Admin|Usuario|Soporte'])->group(function () {
    Route::controller(NotificationController::class)->group(function () {
        Route::get('/notificaciones', 'index');
        Route::post('/notificaciones/marcar-leidas', 'marcarNotificacionesComoLeidas');
    });

    Route::controller(SedeController::class)->group(function () {
        Route::get('/elegirsede', 'elegirsede')->name('elegirsede');
        Route::get('/sedes', 'traer');
    });
    Route::controller(UsuarioController::class)->group(function () {
        Route::post('/registrar-sede', 'registrarSedeUnaVez');
    });
});

Route::middleware(['auth', 'role:Soporte'])->group(function () {
    Route::controller(SedeController::class)->group(function () {
        Route::put('/sedes/{sede}', 'actualizarSede')->name('actualizar-sede');
        Route::get('/profile-sedes', 'showSedeForm');
    });
});

Route::middleware(['auth', 'check.sede'])->group(function () {
    Route::middleware(['auth', 'role:Admin'])->group(function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('dashboard');
        });
    });
    Route::middleware(['auth', 'role:Usuario'])->group(function () {
        Route::controller(UsuarioDashboardController::class)->group(function () {
            Route::get('/user-dashboard', 'index')->name('user-dashboard');
        });
    });
    Route::middleware(['auth', 'role:Soporte'])->group(function () {
        Route::controller(SoporteDashboardController::class)->group(function () {
            Route::get('/support-dashboard', 'index')->name('support-dashboard');
        });
    });
});
