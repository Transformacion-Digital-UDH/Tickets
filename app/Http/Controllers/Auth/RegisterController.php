<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Register');
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
        return redirect()->route('elegirsede');
    }
}
