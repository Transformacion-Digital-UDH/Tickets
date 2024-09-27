<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UsuarioController extends Controller
{
    public function soporte()
    {
        return Inertia::render("Admin/Soporte", [
            'success' => session('success'),
        ]);
    }

    public function traerSoporte()
    {
        $soportes = User::with('rol', 'sede')
            ->whereHas('rol', function ($query) {
                $query->where('rol_nombre', 'Soporte');
            })
            ->get();

        return response()->json($soportes);
    }

    public function usuario()
    {
        return Inertia::render("Admin/Usuario", [
            'success' => session('success'),
        ]);
    }

    public function traerUsuario()
    {
        $usuarios = User::with('rol', 'sede')
            ->whereHas('rol', function ($query) {
                $query->where('rol_nombre', 'Usuario');
            })
            ->get();

        return response()->json($usuarios);
    }

    public function storeSoporte(Request $request)
    {
        // Validar los datos del request
        $validarDatos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'celular' => 'required|string|max:30',
            'sed_id' => 'required|exists:sedes,id',
            'password' => 'required|string|min:8',
            'activo' => 'boolean',
        ]);
    
        // Buscar el rol de 'Soporte'
        $soporte_rol = Rol::where('rol_nombre', 'Soporte')->first();
    
        // Verificar si el rol fue encontrado
        if (!$soporte_rol) {
            return response()->json(['message' => 'Rol Soporte no encontrado'], 404);
        }
    
        // Encriptar la contraseña
        $validarDatos['password'] = bcrypt($validarDatos['password']);
        $validarDatos['rol_id'] = $soporte_rol->id; // Asignar el rol_id
    
        try {
            // Crear el nuevo usuario
            $soporte = User::create($validarDatos);
    
            // Asignar el rol de 'Soporte' al nuevo usuario
            $soporte->assignRole('Soporte');
    
            return response()->json([
                'message' => 'Soporte técnico creado exitosamente',
                'soporte' => $soporte,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al crear soporte técnico: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storeUsuario(Request $request)
    {
        $validarDatos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'celular' => 'required|string|max:30',
            'sed_id' => 'required|exists:sedes,id',
            'password' => 'required|string|min:8',
            'activo' => 'boolean',
        ]);

        $usuario_rol = Rol::where('rol_nombre', 'Usuario')->first();

        if (!$usuario_rol) {
            return response()->json(['message' => 'Rol Usuario no encontrado'], 404);
        }

        $validarDatos['password'] = bcrypt($validarDatos['password']);
        $validarDatos['rol_id'] = $usuario_rol->id;

        $usuario = User::create($validarDatos);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'usuario' => $usuario,
        ], 201);
    }

    public function updateSoporte(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'celular' => 'required|string|max:30',
                'sed_id' => 'required|exists:sedes,id',
                'password' => 'nullable|string|min:8',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Hubo errores en la validación',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $data = $request->only(['name', 'email', 'celular', 'sed_id', 'activo']);
            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }

            $user->update($data);

            return response()->json([
                'status' => true,
                'msg' => 'Soporte Técnico actualizado correctamente',
                'user' => $user,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Soporte técnico no encontrado',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Error al actualizar el soporte técnico: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateUsuario(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'celular' => 'required|string|max:30',
                'sed_id' => 'required|exists:sedes,id',
                'password' => 'nullable|string|min:8',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Hubo errores en la validación',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $data = $request->only(['name', 'email', 'celular', 'sed_id', 'activo']);
            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }

            $user->update($data);

            return response()->json([
                'status' => true,
                'msg' => 'Usuario actualizado correctamente',
                'user' => $user,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Usuario no encontrado',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Error al actualizar al usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function eliminarSoporte($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->delete();

            return response()->json([
                'status' => true,
                'msg' => 'Soporte técnico eliminado exitosamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Soporte técnico no encontrado.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Ocurrió un error al eliminar al soporte técnico: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function eliminarUsuario($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->delete();

            return response()->json([
                'status' => true,
                'msg' => 'Usuario eliminado exitosamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Usuario no encontrado.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Ocurrió un error al eliminar al usuario: ' . $e->getMessage(),
            ], 500);
        }
    }
}
