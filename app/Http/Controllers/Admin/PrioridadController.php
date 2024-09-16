<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prioridad;

class PrioridadController extends Controller
{
    public function traer() {
        $prioridades = Prioridad::all();
        return response()->json($prioridades);
    }
}