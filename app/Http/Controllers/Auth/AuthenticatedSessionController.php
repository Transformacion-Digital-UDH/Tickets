<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
            'error' => session('error'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

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

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
