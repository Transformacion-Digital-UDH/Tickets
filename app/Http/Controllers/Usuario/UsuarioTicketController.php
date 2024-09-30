<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UsuarioTicketController extends Controller
{
    public function index()
    {
        return Inertia::render('Usuario/Ticket', [
            'success' => session('success'),
        ]);
    }

    public function traer()
    {
        $userId = Auth::id();

        $tickets = Ticket::with('prioridad', 'user', 'categoria', 'pabellon', 'aula')
            ->where('use_id', $userId)
            ->get();

        return response()->json($tickets);
    }

    public function create()
    {
        $tickets = Ticket::with(['aula', 'aula.pabellon', 'prioridad', 'categoria', 'pabellon'])->get();

        return Inertia::render('Usuario/CrearTicket', [
            'tickets' => $tickets,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tic_titulo' => 'required|string|max:255',
            'tic_descripcion' => 'required|string',
            'pri_id' => 'required|exists:prioridads,id',
            'cat_id' => 'required|exists:categorias,id',
            'pab_id' => 'required|exists:pabellons,id',
            'aul_id' => 'required|exists:aulas,id',
        ]);

        $ticket = Ticket::create([
            'tic_titulo' => $validatedData['tic_titulo'],
            'tic_descripcion' => $validatedData['tic_descripcion'],
            'pri_id' => $validatedData['pri_id'],
            'use_id' => Auth::id(),
            'cat_id' => $validatedData['cat_id'],
            'pab_id' => $validatedData['pab_id'],
            'aul_id' => $validatedData['aul_id'],
            'tic_estado' => 'Abierto',
            'tic_activo' => true,
        ]);

        return response()->json($ticket, 201);
    }

    public function update(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tic_titulo' => 'required|string|max:255',
                'tic_descripcion' => 'required|string|max:400',
                'cat_id' => 'required|exists:categorias,id',
                'pri_id' => 'required|exists:prioridads,id',
                'pab_id' => 'required|exists:pabellons,id',
                'aul_id' => 'required|exists:aulas,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Hubo errores en la validación',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $data = $request->only(['tic_titulo', 'tic_descripcion', 'cat_id', 'pri_id', 'pab_id', 'aul_id']);

            $ticket->update($data);

            return response()->json([
                'status' => true,
                'msg' => 'Ticket actualizado correctamente',
                'ticket' => $ticket,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Ticket no encontrado',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Error al actualizar al ticket: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function eliminar($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            $ticket->delete();

            return response()->json([
                'status' => true,
                'msg' => 'Ticket eliminado exitosamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Ticket no encontrado.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Ocurrió un error al eliminar al ticket: ' . $e->getMessage(),
            ], 500);
        }
    }
}
