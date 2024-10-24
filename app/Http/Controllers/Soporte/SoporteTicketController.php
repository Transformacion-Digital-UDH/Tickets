<?php

namespace App\Http\Controllers\Soporte;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketStatusChanged;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SoporteTicketController extends Controller
{
    public function index()
    {
        return Inertia::render('Soporte/Ticket', [
            'success' => session('success'),
        ]);
    }

    public function aceptarTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->tic_estado === 'Asignado' || $ticket->tic_estado === 'Reabierto') {
            $ticket->update(['tic_estado' => 'En progreso']);

            $personName = auth()->user()->name;

            $administradores = User::role('Admin')->get();
            foreach ($administradores as $admin) {
                $admin->notify(new TicketStatusChanged($ticket, 'En progreso', $personName));
            }

            $usuario = $ticket->user;
            if ($usuario) {
                $usuario->notify(new TicketStatusChanged($ticket, 'En progreso', $personName));
            }

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

    public function obtenerTickets()
    {
        $userId = auth()->id();

        $tickets = Ticket::whereHas('asignados', function ($query) use ($userId) {
            $query->where('sop_id', $userId);
        })->with(['prioridad', 'categoria', 'user'])
            ->distinct()
            ->get();

        return response()->json($tickets);
    }

    public function finalizarTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update(['tic_estado' => 'Resuelto']);

        $personName = auth()->user()->name;

        $administradores = User::role('Admin')->get();
        foreach ($administradores as $admin) {
            $admin->notify(new TicketStatusChanged($ticket, 'Resuelto', $personName));
        }

        $usuario = $ticket->user;
        if ($usuario) {
            $usuario->notify(new TicketStatusChanged($ticket, 'Resuelto', $personName));
        }

        return response()->json([
            'status' => true,
            'msg' => 'Ticket finalizado correctamente.',
            'ticket' => $ticket,
        ]);
    }
}
