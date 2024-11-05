<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignado;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketStatusChanged;
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

    public function traerPaginated()
    {
        $totalTickets = Ticket::count();

        $tickets = Ticket::with('prioridad', 'user', 'categoria', 'pabellon', 'aula', 'soporteActual.soporte')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $tickets->getCollection()->transform(function ($ticket, $key) use ($totalTickets, $tickets) {
            $ticket->row_number = $totalTickets - (($tickets->currentPage() - 1) * $tickets->perPage() + $key);
            return $ticket;
        });

        return response()->json($tickets);
    }

    public function traer()
    {
        $tickets = Ticket::with('prioridad', 'user', 'categoria', 'pabellon', 'aula', 'soporteActual.soporte')->get();
        return response()->json($tickets);
    }

    public function storeImage(Request $request)
    {
        try {
            $request->validate([
                'tic_archivo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            ]);

            $filePath = null;
            if ($request->hasFile('tic_archivo')) {
                $filePath = $request->file('tic_archivo')->store('ticket_images', 'public');
            }

            $ticket = Ticket::create([
                'tic_archivo' => $filePath,
            ]);

            return response()->json([
                'status' => true,
                'msg' => 'Archivo actualizado correctamente',
                'ticket' => $ticket,
                'image_url' => Storage::url($ticket->tic_archivo),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tic_titulo' => 'required|string|max:255',
            'tic_descripcion' => 'required|string|max:400',
            'pri_id' => 'required|exists:prioridads,id',
            'use_id' => 'required|exists:users,id',
            'cat_id' => 'required|exists:categorias,id',
            'pab_id' => 'required|exists:pabellons,id',
            'aul_id' => 'required|exists:aulas,id',
        ]);

        $ticket = Ticket::create([
            'tic_titulo' => $validatedData['tic_titulo'],
            'tic_descripcion' => $validatedData['tic_descripcion'],
            'pri_id' => $validatedData['pri_id'],
            'use_id' => $validatedData['use_id'],
            'cat_id' => $validatedData['cat_id'],
            'pab_id' => $validatedData['pab_id'],
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
            'pri_id' => 'required|exists:prioridads,id',
            'es_asignado' => 'boolean',
        ]);

        $ticket = Ticket::findOrFail($id);
        $personName = auth()->user()->name;
        $asignado = Asignado::where('tic_id', $ticket->id)->first();

        if ($asignado) {
            $asignado->update([
                'sop_id' => $validatedData['sop_id'],
                'es_asignado' => $validatedData['es_asignado'] ?? true,
            ]);

            $ticket->update([
                'tic_estado' => 'Asignado',
                'sop_id' => $validatedData['sop_id'],
                'pri_id' => $validatedData['pri_id'],
            ]);

            $soportes = User::role('Soporte')->get();
            foreach ($soportes as $soporte) {
                $soporte->notify(new TicketStatusChanged($ticket, 'Asignado', $personName));
            }

            return response()->json([
                'status' => true,
                'msg' => 'Soporte y prioridad actualizados, estado actualizado a "Asignado".',
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
                'pri_id' => $validatedData['pri_id'],
            ]);

            $soportes = User::role('Soporte')->get();
            foreach ($soportes as $soporte) {
                $soporte->notify(new TicketStatusChanged($ticket, 'Asignado', $personName));
            }

            return response()->json([
                'status' => true,
                'msg' => 'Soporte y prioridad asignados, estado actualizado a "Asignado".',
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
                'tic_activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Hubo errores en la validación',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $ticket->update([
                'tic_titulo' => $request->input('tic_titulo'),
                'tic_descripcion' => $request->input('tic_descripcion'),
                'use_id' => $request->input('use_id'),
                'cat_id' => $request->input('cat_id'),
                'pri_id' => $request->input('pri_id'),
                'pab_id' => $request->input('pab_id'),
                'aul_id' => $request->input('aul_id'),
                'tic_activo' => filter_var($request->input('tic_activo'), FILTER_VALIDATE_BOOLEAN),
            ]);

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
                'tic_estado' => 'required|string|in:Abierto,En progreso,Cerrado,Finalizado,Reabierto',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Errores de validación',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $personName = auth()->user()->name;

            $ticket->update([
                'tic_estado' => $request->input('tic_estado'),
            ]);

            if ($request->input('tic_estado') === 'Cerrado') {
                $user = $ticket->user;
                if ($user) {
                    $user->notify(new TicketStatusChanged($ticket, 'Cerrado', $personName));
                }

                if ($ticket->soporteActual && $ticket->soporteActual->soporte) {
                    $soporteAsignado = $ticket->soporteActual->soporte;
                    $soporteAsignado->notify(new TicketStatusChanged($ticket, 'Cerrado', $personName));
                }
            } elseif ($request->input('tic_estado') === 'Reabierto') {
                $user = $ticket->user;
                if ($user) {
                    $user->notify(new TicketStatusChanged($ticket, 'Reabierto', $personName));
                }

                if ($ticket->soporteActual && $ticket->soporteActual->soporte) {
                    $soporteAsignado = $ticket->soporteActual->soporte;
                    $soporteAsignado->notify(new TicketStatusChanged($ticket, 'Reabierto', $personName));
                }
            }

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
                if (Storage::disk('public')->exists($ticket->tic_archivo)) {
                    Storage::disk('public')->delete($ticket->tic_archivo);
                }
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
