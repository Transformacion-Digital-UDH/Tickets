<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UsuarioDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Usuario/Dashboard', [
            'success' => session('success'),
        ]);
    }

    public function traer()
    {
        $userId = Auth::id();

        $openTickets = Ticket::where('use_id', $userId)
            ->where('tic_estado', 'Abierto')
            ->count();

        $inProgressTickets = Ticket::where('use_id', $userId)
            ->where('tic_estado', 'En progreso')
            ->count();

        $closeTickets = Ticket::where('use_id', $userId)
            ->where('tic_estado', 'Cerrado')
            ->count();

        $todayTickets = Ticket::where('use_id', $userId)
            ->whereDate('created_at', now())
            ->count();

        return response()->json([
            'openTickets' => $openTickets,
            'inProgressTickets' => $inProgressTickets,
            'closeTickets' => $closeTickets,
            'todayTickets' => $todayTickets,
        ]);
    }
}
