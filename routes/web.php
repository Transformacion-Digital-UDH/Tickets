<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return Inertia::render('Admin/Dashboard', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        } elseif ($user->hasRole('soporte')) {
            return Inertia::render('Soporte/Dashboard', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        } elseif ($user->hasRole('usuario')) {
            return Inertia::render('Usuario/Dashboard', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        }
    }
    return redirect()->route('login');
});

require __DIR__ . '/admin.php';
require __DIR__ . '/soporte.php';
require __DIR__ . '/usuario.php';
require __DIR__ . '/auth.php';
