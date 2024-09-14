<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Validator;
class AulaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Aula');
    }

    public function traer()
    {
        $aulas = Aula::with('pabellon')->get();
        return response()->json($aulas);
    }
    public function store(Request $request)
    {
        $validarDatos = $request->validate([
            'aul_numero' => 'required|string|max:255',
            'pab_id' => 'required|exists:pabellons,id',
            'aul_activo' => 'boolean',
        ]);

        $aula = Aula::create($validarDatos);

        return response()->json([
            'message' => 'Aula creada exitosamente',
            'aula' => $aula
        ], 201);
    }

    public function update(Request $request, $id)
    {
        try {
            $aula = Aula::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'aul_numero' => 'required|string|max:255',
                'pab_id' => 'required|exists:pabellons,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Hubo errores en la validación',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $data = $request->only(['aul_numero', 'pab_id']);

            $aula->update($data);

            return response()->json([
                'status' => true,
                'msg' => 'Aula actualizada correctamente',
                'aula' => $aula
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Aula no encontrada'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Error al actualizar el aula: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function desactivar($id)
    {
        try {
            $aula = Aula::findOrFail($id);

            $aula->update(['aul_activo' => 0]);

            return response()->json([
                'status' => true,
                'msg' => 'Aula desactivada exitosamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Aula no encontrada.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Ocurrió un error al desactivar el aula: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function activar($id)
    {
        try {
            $aula = Aula::findOrFail($id);

            $aula->update(['aul_activo' => 1]);

            return response()->json([
                'status' => true,
                'msg' => 'Aula desactivada exitosamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Aula no encontrada.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Ocurrió un error al desactivar el aula: ' . $e->getMessage(),
            ], 500);
        }
    }
}
