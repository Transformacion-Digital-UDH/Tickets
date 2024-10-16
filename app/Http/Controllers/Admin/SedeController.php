<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SedeController extends Controller
{
    public function elegirsede()
    {
        $user = Auth::user();

        if ($user && $user->sed_id) {
            if ($user->hasRole('Admin')) {
                return redirect()->route('dashboard');
            } elseif ($user->hasRole('Usuario')) {
                return redirect()->route('user-dashboard');
            } elseif ($user->hasRole('Soporte')) {
                return redirect()->route('support-dashboard');
            }
        }

        return Inertia::render('ElegirSedes', ['authUser' => $user]);
    }

    public function index()
    {
        return Inertia::render('Admin/Sedes', [
            'success' => session('success'),
        ]);
    }

    public function traerPaginated()
    {
        $totalSedes = Sede::count();

        if (Auth::user()->hasAnyRole(['Soporte', 'Usuario'])) {
            return response()->json(['sedes' => []], 200);
        }

        $sedes = Sede::where('sed_activo', true)->orderBy('created_at', 'desc')->paginate(5);

        $sedes->getCollection()->transform(function ($sede, $key) use ($totalSedes, $sedes) {
            $sede->row_number = $totalSedes - (($sedes->currentPage() - 1) * $sedes->perPage() + $key);
            return $sede;
        });

        return response()->json($sedes, 200);
    }

    public function traer()
    {
        $sedes = Sede::all();

        return response()->json($sedes, 200);
    }

    public function store(Request $request)
    {
        $validarDatos = $request->validate([
            'sed_nombre' => 'required|string|max:255',
            'sed_direccion' => 'required|string|max:255',
            'sed_ciudad' => 'required|string|max:255',
            'sed_telefono' => 'required|numeric|digits:9',
            'sed_activo' => 'boolean',
        ]);

        $sede = Sede::create($validarDatos);
        return response()->json(['message' => 'Sede creada correctamente', 'sede' => $sede], 201);
    }

    public function upload(Request $request, $id)
    {
        $sede = Sede::findOrFail($id);

        if ($request->hasFile('sed_imagen')) {
            if ($sede->sed_imagen) {
                if (Storage::disk('public')->exists($sede->sed_imagen)) {
                    Storage::disk('public')->delete($sede->sed_imagen);
                }
            }

            $filePath = $request->file('sed_imagen')->store('sede_images', 'public');
            $sede->sed_imagen = $filePath;
            $sede->save();
        }

        return response()->json([
            'status' => true,
            'msg' => 'Archivo actualizado correctamente',
            'sede' => $sede,
            'image_url' => Storage::url($sede->sed_imagen),
        ]);
    }

    public function update(Request $request, Sede $sede)
    {
        $validarDatos = $request->validate([
            'sed_nombre' => 'required|string|max:255',
            'sed_direccion' => 'required|string|max:255',
            'sed_ciudad' => 'required|string|max:255',
            'sed_telefono' => 'required|numeric|digits:9',
            'sed_activo' => 'nullable|boolean',
        ]);

        if ($request->has('sed_activo')) {
            $validarDatos['sed_activo'] = filter_var($request->input('sed_activo'), FILTER_VALIDATE_BOOLEAN);
        }

        $sede->update($validarDatos);

        return response()->json(['message' => 'Sede actualizada correctamente', 'sede' => $sede]);
    }

    public function eliminar(Sede $sede)
    {
        $sede->delete();
        return response()->json(['message' => 'Sede eliminada correctamente', 204]);
    }

    public function desactivar(Sede $sede)
    {
        $sede->update(['sed_activo' => 0]);
        return response()->json(['message' => 'Sede desactivada correctamente'], 200);
    }

    public function activar(Sede $sede)
    {
        $sede->update(['sed_activo' => 1]);
        return response()->json(['message' => 'Sede activada correctamente'], 200);
    }
}
