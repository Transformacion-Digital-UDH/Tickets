<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Route;

Route::get('register', [RegisterController::class, 'create'])
    ->name('register');

Route::post('register', [RegisterController::class, 'store']);

Route::controller(GoogleController::class)->group(function () {
    Route::get('/google/redirect', 'redirect')->name('google');
    Route::get('/google/callback', 'callback');
});
