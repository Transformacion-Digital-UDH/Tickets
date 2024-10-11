<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!FacadesAuth::check()) {
            return redirect()->route('login');
        }

        if(FacadesAuth::user()->rol->rol_nombre !== 'Admin') {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
