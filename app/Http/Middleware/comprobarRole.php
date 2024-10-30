<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Symfony\Component\HttpFoundation\Response;

class comprobarRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!FacadesAuth::check() || FacadesAuth::user()->rol->rol_nombre !== $role) {
            abort(403, "Acceso denegado");
        }

        return $next($request);
    }
}
