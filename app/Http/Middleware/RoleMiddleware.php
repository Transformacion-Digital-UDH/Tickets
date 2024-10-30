<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user() || $request->user()->role !== $role) {
            // Redirigir si el usuario no tiene el rol necesario
            abort(403, 'Acceso no autorizado');
        }

        return $next($request);
    }
}
