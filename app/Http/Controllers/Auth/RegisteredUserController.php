<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Register', [
            'status' => session('status'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'ends_with:udh.edu.pe', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'sed_id' => ['nullable', 'exists:sedes,id'],
        ], [
            'email.ends_with' => 'Ingrese un correo institucional de la UDH.',
        ]);
        $se_registro = User::where('email', $request->email)->first();
        if (!$se_registro) {
            $user = new User();
            $user->name = $request->name;
            $user->apellidos = $request->apellidos;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->sed_id = $request->sed_id ?? null;
            $user->assignRole('Usuario');
            $user->save();
            Auth::login($user);
        } else {
            $se_registro->name = $request->name;
            $se_registro->apellidos = $request->apellidos;
            $se_registro->password = Hash::make($request->password);
            $se_registro->sed_id = $request->sed_id ?? null;
            $se_registro->save();
            Auth::login($se_registro);
        }
        // Aca extraeremos el nombre del rol...
        $roleName = Auth::user()->rol->rol_nombre;

        // Para luego compararlo con una condicional y poder redirigir a su ruta correcta...
        if ($roleName === 'Admin') {
            return redirect()->intended(RouteServiceProvider::$ADMINHOME)
                ->with('success', 'Ha iniciado sesión como administrador.');
        } elseif ($roleName === 'Soporte') {
            return redirect()->intended(RouteServiceProvider::$SUPPORTHOME)
                ->with('success', 'Ha iniciado sesión como soporte técnico.');
        } else {
            return redirect()->intended(RouteServiceProvider::$USERHOME)
                ->with('success', 'Ha iniciado sesión como usuario.');
        }
    }
}
