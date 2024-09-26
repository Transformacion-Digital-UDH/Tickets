<?php

namespace App\Http\Controllers\Soporte;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class SoporteDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Soporte/Dashboard', [
            'success' => session('success'),
        ]);
    }
}
