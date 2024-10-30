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
        return Inertia::render('Admin/Pabellon', [
            'success' => session('success'),
        ]);
    }

    public function traerPaginated()
    {
        $totalPabellones = Pabellon::count();

        $pabellones = Pabellon::with('sede')->orderBy('created_at', 'desc')->paginate(5);

        $pabellones->getCollection()->transform(function ($pabellon, $key) use ($totalPabellones, $pabellones) {
            $pabellon->row_number = $totalPabellones - (($pabellones->currentPage() - 1) * $pabellones->perPage() + $key);
            return $pabellon;
        });

        return response()->json($pabellones);
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
            'pab_activo' => 'nullable|boolean',
        ]);

        if ($request->has('pab_activo')) {
            $validarDatos['pab_activo'] = filter_var($request->input('pab_activo'), FILTER_VALIDATE_BOOLEAN);
        }

        $pabellon->update($validarDatos);

        return response()->json(['message' => 'Pabellón actualizado correctamente', 'pabellon' => $pabellon]);
    }

    public function eliminar($id)
    {
        try {
            $pabellon = Pabellon::findOrFail($id);

            $pabellon->delete();

            return response()->json([
                'status' => true,
                'msg' => 'Pabellon eliminado exitosamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Pabellon no encontrada.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Ocurrió un error al eliminar el pabellon: ' . $e->getMessage(),
            ], 500);
        }
    }
}
