<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AulaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Aula', [
            'success' => session('success'),
        ]);
    }

    public function traerPaginated()
    {
        $totalAulas = Aula::where('aul_activo', true)->count();
        
        $aulas = Aula::with('pabellon')->orderBy('created_at', 'desc')->paginate(5);

        $aulas->getCollection()->transform(function ($aula, $key) use ($totalAulas, $aulas) {
            $aula->row_number = $totalAulas - (($aulas->currentPage() - 1) * $aulas->perPage() + $key);
            return $aula;
        });

        return response()->json($aulas);
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

    public function update(Request $request, Aula $aula)
    {
        $validarDatos = $request->validate([
            'aul_numero' => 'required|string|max:255',
            'pab_id' => 'required|exists:pabellons,id',
            'aul_activo' => 'nullable|boolean',
        ]);

        if ($request->has('aul_activo')) {
            $validarDatos['aul_activo'] = filter_var($request->input('aul_activo'), FILTER_VALIDATE_BOOLEAN);
        }

        $aula->update($validarDatos);

        return response()->json(['message' => 'Aula actualizado correctamente', 'aula' => $aula]);
    }

    public function eliminar($id)
    {
        try {
            $aula = Aula::findOrFail($id);

            $aula->delete();

            return response()->json([
                'status' => true,
                'msg' => 'Aula elimanada exitosamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Aula no encontrada.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Ocurrió un error al eliminar el aula: ' . $e->getMessage(),
            ], 500);
        }
    }
}
