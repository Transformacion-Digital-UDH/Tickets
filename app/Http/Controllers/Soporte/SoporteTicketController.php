<?php
namespace App\Http\Controllers\Soporte;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Asignado;
use Illuminate\Http\Request;
use Inertia\Inertia; // Asegúrate de importar Inertia

class SoporteTicketController extends Controller
{
    // Método para mostrar la vista de soporte
    public function index()
    {
        return Inertia::render('Soporte/Ticket', [
            'success' => session('success'),
        ]);
    }

    // Método para obtener tickets asignados al soporte
    public function obtenerTickets()
    {
        $userId = auth()->id(); // Obtener el ID del usuario autenticado (soporte)

        // Utilizar la relación asignados para obtener los tickets
        $tickets = Ticket::whereHas('asignados', function ($query) use ($userId) {
            $query->where('sop_id', $userId);
        })->with([
                    'asignados' => function ($query) use ($userId) {
                        $query->where('sop_id', $userId);
                    }
                ])->distinct()->get();

        // Muestra los tickets filtrados
        return response()->json($tickets); // Retornar los tickets en formato JSON
    }

    // Método para finalizar un ticket
    public function finalizarTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id); // Buscar el ticket por ID

        // Cambiar el estado del ticket a "Finalizado"
        $ticket->update(['tic_estado' => 'Finalizado']);

        return response()->json([
            'status' => true,
            'msg' => 'Ticket finalizado correctamente.',
            'ticket' => $ticket,
        ]);
    }
}