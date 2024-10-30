<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'success' => session('success'),
        ]);
    }

    public function traer()
    {
        $openTickets = Ticket::where('tic_estado', 'Abierto')
            ->where('tic_activo', true)
            ->count();

        $asignTickets = Ticket::where('tic_estado', 'Asignado')
            ->where('tic_activo', true)
            ->count();

        $inProgressTickets = Ticket::where('tic_estado', 'En progreso')
            ->where('tic_activo', true)
            ->count();

        $resultTickets = Ticket::where('tic_estado', 'Resuelto')
            ->where('tic_activo', true)
            ->count();

        $closeTickets = Ticket::where('tic_estado', 'Cerrado')
            ->where('tic_activo', true)
            ->count();

        $reopenTickets = Ticket::where('tic_estado', 'Reabierto')
            ->where('tic_activo', true)
            ->count();

        $todayTickets = Ticket::where('tic_activo', true)
            ->whereDate('created_at', now())
            ->count();

        return response()->json([
            'openTickets' => $openTickets,
            'asignTickets' => $asignTickets,
            'inProgressTickets' => $inProgressTickets,
            'resultTickets' => $resultTickets,
            'closeTickets' => $closeTickets,
            'reopenTickets' => $reopenTickets,
            'todayTickets' => $todayTickets,
        ]);
    }
}
