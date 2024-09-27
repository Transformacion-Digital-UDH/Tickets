<?php

use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\PabellonController;
use App\Http\Controllers\Admin\PrioridadController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

require __DIR__ . '/admin.php';
require __DIR__ . '/soporte.php';
require __DIR__ . '/usuario.php';
require __DIR__ . '/auth.php';
