<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;
class CategoriaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Categoria');
    }

    public function traer()
    {
        $categorias = Categoria::all();
        return response()->json($categorias);
    }

    public function store(Request $request)
    {
        $validarDatos = $request->validate([
            'cat_nombre' => 'required|string|max:255',
            'cat_activo' => 'boolean',
        ]);

        $categoria = Categoria::create($validarDatos);
        return response()->json($categoria, 201);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validarDatos = $request->validate([
            'cat_nombre' => 'required|string|max:255',
            'cat_activo' => 'nullable|boolean',
        ]);

        $categoria->update($validarDatos);

        return response()->json(['message' => 'Categoría actualizada correctamente', 'categoria' => $categoria]);
    }

    public function eliminar(Categoria $categoria)
    {
        $categoria->delete();
        return response()->json(['message' => 'Categoría desactivada correctamente'], 200);
    }
}
