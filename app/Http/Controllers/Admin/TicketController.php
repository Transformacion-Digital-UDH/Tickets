<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignado;
use App\Models\Ticket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Ticket', [
            'success' => session('success'),
        ]);
    }

    public function traer()
    {
        $tickets = Ticket::with('prioridad', 'user', 'categoria', 'pabellon', 'aula', 'soporteActual.soporte')->get();
        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tic_titulo' => 'required|string|max:255',
            'tic_descripcion' => 'required|string|max:400',
            'tic_archivo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pri_id' => 'required|exists:prioridads,id',
            'use_id' => 'required|exists:users,id',
            'cat_id' => 'required|exists:categorias,id',
            'pab_id' => 'required|exists:pabellons,id',
            'aul_id' => 'required|exists:aulas,id',
        ]);

        $filePath = null;
        if ($request->hasFile('tic_archivo')) {
            $filePath = $request->file('tic_archivo')->store('ticket_images', 'public');
        }

        $ticket = Ticket::create([
            'tic_titulo' => $validatedData['tic_titulo'],
            'tic_descripcion' => $validatedData['tic_descripcion'],
            'tic_archivo' => $filePath,
            'pri_id' => $validatedData['pri_id'],
            'use_id' => $validatedData['use_id'],
            'cat_id' => $validatedData['cat_id'],
            'pab_id' => $validatedData['pab_id'],
            'aul_id' => $validatedData['aul_id'],
            'aul_id' => $validatedData['aul_id'],
            'tic_estado' => 'Abierto',
            'tic_activo' => true,
        ]);

        return response()->json($ticket, 201);
    }

    public function asignarSoporte(Request $request, $id)
    {
        $validatedData = $request->validate([
            'sop_id' => 'required|exists:users,id',
            'es_asignado' => 'boolean',
        ]);

        $ticket = Ticket::findOrFail($id);

        $asignado = Asignado::where('tic_id', $ticket->id)->first();

        if ($asignado) {
            $asignado->update([
                'sop_id' => $validatedData['sop_id'],
                'es_asignado' => $validatedData['es_asignado'] ?? true,
            ]);
            $ticket->update([
                'tic_estado' => 'Asignado',
                'sop_id' => $validatedData['sop_id'],
            ]);

            return response()->json([
                'status' => true,
                'msg' => 'Soporte actualizado y estado actualizado a "Asignado".',
                'ticket' => $ticket,
            ]);
        } else {
            Asignado::create([
                'tic_id' => $ticket->id,
                'sop_id' => $validatedData['sop_id'],
                'es_asignado' => $validatedData['es_asignado'] ?? true,
            ]);

            $ticket->update([
                'tic_estado' => 'Asignado',
                'sop_id' => $validatedData['sop_id'],
            ]);

            return response()->json([
                'status' => true,
                'msg' => 'Soporte asignado y estado actualizado a "Asignado".',
                'ticket' => $ticket,
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tic_titulo' => 'required|string|max:255',
                'tic_descripcion' => 'required|string|max:400',
                'use_id' => 'required|exists:users,id',
                'cat_id' => 'required|exists:categorias,id',
                'pri_id' => 'required|exists:prioridads,id',
                'pab_id' => 'required|exists:pabellons,id',
                'aul_id' => 'required|exists:aulas,id',
                'tic_estado' => 'nullable|string|in:Abierto,En progreso,Cerrado,Finalizado',
                'tic_activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Hubo errores en la validación',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $ticket->update($request->only([
                'tic_titulo', 'tic_descripcion', 'use_id', 'cat_id', 'pri_id',
                'pab_id', 'aul_id', 'tic_estado', 'tic_activo',
            ]));

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

    public function updateEstado(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tic_estado' => 'required|string|in:Abierto,En progreso,Cerrado,Finalizado',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Errores de validación',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $ticket->update([
                'tic_estado' => $request->input('tic_estado'),
            ]);

            return response()->json([
                'status' => true,
                'msg' => 'Estado del ticket actualizado correctamente',
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
                'msg' => 'Error al actualizar el estado del ticket: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function upload(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($request->hasFile('tic_archivo')) {
            if ($ticket->tic_archivo) {
                Storage::disk('public')->delete($ticket->tic_archivo);
            }

            $filePath = $request->file('tic_archivo')->store('ticket_images', 'public');
            $ticket->tic_archivo = $filePath;
            $ticket->save();
        }

        return response()->json([
            'status' => true,
            'msg' => 'Archivo actualizado correctamente',
            'ticket' => $ticket,
            'image_url' => Storage::url($ticket->tic_archivo),
        ]);
    }

    public function eliminar($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            if ($ticket->tic_archivo) {
                Storage::disk('public')->delete($ticket->tic_archivo);
            }

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
