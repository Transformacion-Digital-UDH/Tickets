<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignado;
use Illuminate\Http\Request;

class AsignadoController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tic_id' => 'required|exists:tickets,id',
            'use_id' => 'required|exists:users,id',
            'es_asignado' => 'boolean',
        ]);

        $ticket = Asignado::create([
            'tic_id' => $validatedData['tic_id'],
            'use_id' => $validatedData['use_id'],
            'es_asignado' => $validatedData['es_asignado'],
        ]);

        return response()->json($ticket, 201);
    }
}
