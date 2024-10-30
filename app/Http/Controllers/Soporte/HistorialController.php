<?php

namespace App\Http\Controllers\Soporte;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class HistorialController extends Controller
{
    public function index()
    {
        return Inertia::render('Soporte/Historial', [
            'success' => session('success'),
        ]);
    }
}
