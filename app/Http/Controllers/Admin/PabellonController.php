<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pabellon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PabellonController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Pabellon');
    }

    public function traer()
    {
        $pabellones = Pabellon::with('sede')->get();
        return response()->json($pabellones);
    }

    public function store(Request $request)
    {
        $validarDatos = $request->validate([
            'pab_nombre' => 'required|string|max:255',
            'sed_id' => 'required|exists:sedes,id',
            'sed_activo' => 'boolean',
        ]);

        $pabellon = Pabellon::create($validarDatos);
        return response()->json($pabellon, 201);
    }

    public function update(Request $request, Pabellon $pabellon)
    {
        $validarDatos = $request->validate([
            'sed_id' => 'required|exists:sedes,id',
            'pab_nombre' => 'required|string|max:255',
            'pab_activo' => 'boolean',
        ]);

        $pabellon->update($validarDatos);

        return response()->json(['message' => 'Pabellón actualizado correctamente', 'pabellon' => $pabellon]);
    }

    public function desactivar($id)
    {
        try {
            $pabellon = Pabellon::findOrFail($id);

            $pabellon->update(['pab_activo' => 0]);

            return response()->json([
                'status' => true,
                'msg' => 'Pabellon desactivado exitosamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Pabellon no encontrada.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Ocurrió un error al desactivar el pabellon: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function activar($id)
    {
        try {
            $pabellon = Pabellon::findOrFail($id);

            $pabellon->update(['pab_activo' => 1]);

            return response()->json([
                'status' => true,
                'msg' => 'Pabellon desactivado exitosamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Pabellon no encontrada.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Ocurrió un error al desactivar el pabellon: ' . $e->getMessage(),
            ], 500);
        }
    }
}
