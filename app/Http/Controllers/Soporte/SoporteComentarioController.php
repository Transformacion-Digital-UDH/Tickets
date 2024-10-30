<?php

namespace App\Http\Controllers\Soporte;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SoporteComentarioController extends Controller
{
    public function verComentarios(Ticket $ticket)
    {
        $comentarios = Comentario::where('tic_id', $ticket->id)->with('user')->get();

        return Inertia::render('Soporte/Comentario', [
            'ticket' => $ticket,
            'comentarios' => $comentarios,
            'success' => session('success'),
        ]);
    }

    public function guardarComentario(Request $request, Ticket $ticket)
    {
        $request->validate([
            'com_texto' => 'required|string|max:1000',
            'com_adjunto' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls|max:2048',
        ]);

        $archivoAdjunto = null;
        if ($request->hasFile('com_adjunto')) {
            $archivoAdjunto = $request->file('com_adjunto')->store('adjuntos', 'public');
        }

        $comentario = Comentario::create([
            'tic_id' => $ticket->id,
            'use_id' => $request->user()->id,
            'com_texto' => $request->com_texto,
            'com_adjunto' => $archivoAdjunto,
        ])->load('user');

        return response()->json(['comentario' => $comentario], 201);
    }
}
