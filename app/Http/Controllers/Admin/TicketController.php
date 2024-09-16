<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;
class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/Ticket');
    }

    public function traer()
    {
        $tickets = Ticket::with('prioridad', 'user', 'categoria')->get();
        return response()->json($tickets);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validarDatos = $request->validate([
            'use_id' => 'required|exists:users,id',
            'cat_id' => 'required|exists:categorias,id',
            'pri_id' => 'required|exists:prioridads,id',
            'pab_id' => 'required|exists:pabellons,id',
            'tic_titulo' => 'required|string|max:255',
            'tic_descripcion' => 'required|string|max:255',
            'tic_archivo' => 'required|string|max:255',
            'tic_estado' => 'required|string|max:255',
            'tic_activo' => 'boolean',
        ]);

        $ticket = Ticket::create($validarDatos);

        return response()->json([
            'message' => 'Ticket creado exitosamente',
            'ticket' => $ticket
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
