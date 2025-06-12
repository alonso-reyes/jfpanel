<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckObra
{
    /*public function handle(Request $request, Closure $next)
    {
        // Verifica si el usuario tiene una obra asignada
        if (!Auth::user() || !Auth::user()->obra_id) {
            // Si no tiene obra, redirige a la selección de obra
            return redirect()->route('/dashboard');
        }

        return $next($request);
    }*/
}