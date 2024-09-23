<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Usuario/Dashboard', [
            'success' => session('success'),
        ]);
    }
}

