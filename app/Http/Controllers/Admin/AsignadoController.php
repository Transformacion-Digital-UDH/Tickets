<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignado;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsignadoController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tic_id' => 'required|exists:tickets,id',
                'use_id' => 'required|exists:users,id',
                'es_asignado' => 'boolean',
            ]);

            $asignado = Asignado::create([
                'tic_id' => $validatedData['tic_id'],
                'use_id' => $validatedData['use_id'],
                'es_asignado' => $validatedData['es_asignado'] ?? true,
            ]);

            $ticket = Ticket::find($validatedData['tic_id']);
            $ticket->estado = 'asignado';
            $ticket->save();

            return response()->json($asignado, 201);
        } catch (\Exception $e) {
            Log::error('Error al asignar el soporte: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error interno del servidor al asignar soporte técnico.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
