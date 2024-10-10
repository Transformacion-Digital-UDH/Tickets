<?php

namespace App\Http\Controllers\Soporte;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SoporteTicketController extends Controller
{
    // Método para mostrar la vista de soporte
    public function index()
    {
        return Inertia::render('Soporte/Ticket', [
            'success' => session('success'),
        ]);
    }

    // Método para aceptar un ticket y cambiar su estado a "En progreso"
    public function aceptarTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id); // Buscar el ticket por ID

        // Verificar si el ticket está en estado Asignado o Reabierto
        if ($ticket->tic_estado === 'Asignado' || $ticket->tic_estado === 'Reabierto') {
            // Cambiar el estado del ticket a "En progreso"
            $ticket->update(['tic_estado' => 'En progreso']);

            return response()->json([
                'status' => true,
                'msg' => 'Ticket aceptado y cambiado a En progreso.',
                'ticket' => $ticket,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'El ticket no se puede aceptar porque no está en estado Asignado o Reabierto.',
            ], 400);
        }
    }


    // Método para obtener tickets asignados al soporte
    public function obtenerTickets()
    {
        $userId = auth()->id(); // Obtener el ID del usuario autenticado (soporte)

        // Obtener los tickets asignados al soporte
        $tickets = Ticket::whereHas('asignados', function ($query) use ($userId) {
            $query->where('sop_id', $userId);
        })->with(['prioridad', 'categoria', 'user']) // Asegurarse de cargar relaciones
            ->distinct()
            ->get();

        // Retornar los tickets en formato JSON
        return response()->json($tickets);
    }

    // Método para finalizar un ticket
    public function finalizarTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id); // Buscar el ticket por ID

        // Cambiar el estado del ticket a "Finalizado"
        $ticket->update(['tic_estado' => 'Resuelto']);

        return response()->json([
            'status' => true,
            'msg' => 'Ticket finalizado correctamente.',
            'ticket' => $ticket,
        ]);
    }
}
