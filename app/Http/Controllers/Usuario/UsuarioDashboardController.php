<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UsuarioDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Usuario/Dashboard', [
            'success' => session('success'),
        ]);
    }
}
