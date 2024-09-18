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
        $tickets = Ticket::with('prioridad', 'user', 'categoria', 'pabellon')->get();
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
        $validatedData = $request->validate([
            'tic_titulo' => 'required|string|max:255',
            'tic_descripcion' => 'required|string',
            'pri_id' => 'required|exists:prioridads,id',
            'use_id' => 'required|exists:users,id',
            'cat_id' => 'required|exists:categorias,id',
            'pab_id' => 'required|exists:pabellons,id',
        ]);

        $ticket = Ticket::create([
            'tic_titulo' => $validatedData['tic_titulo'],
            'tic_descripcion' => $validatedData['tic_descripcion'],
            'pri_id' => $validatedData['pri_id'],
            'use_id' => $validatedData['use_id'],
            'cat_id' => $validatedData['cat_id'],
            'pab_id' => $validatedData['pab_id'],
            'tic_estado' => 'Abierto',
            'tic_activo' => true,
        ]);

        return response()->json($ticket, 201);
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
