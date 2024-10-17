<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComentarioController extends Controller
{
    public function verComentarios(Ticket $ticket)
    {
        $comentarios = Comentario::where('tic_id', $ticket->id)->with('user')->get();

        return Inertia::render('Admin/Comentario', [
            'ticket' => $ticket,
            'comentarios' => $comentarios,
            'success' => session('success'),
        ]);
    }

    public function guardarComentario(Request $request, Ticket $ticket)
    {
        $request->validate([
            'com_texto' => 'required|string|max:1000',
        ]);

        $comentario = Comentario::create([
            'tic_id' => $ticket->id,
            'use_id' => $request->user()->id,
            'com_texto' => $request->com_texto,
        ])->load('user');;

        return response()->json(['comentario' => $comentario], 201);
    }
}
