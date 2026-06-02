<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRol
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verificamos si está logueado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Verificamos si su rol está en la lista de roles permitidos para esta ruta
        if (!in_array(Auth::user()->rol, $roles)) {
            // Si no tiene permiso, le mostramos un error 403 (Acceso Denegado)
            abort(403, 'ACCESO DENEGADO: Tu rol de "'. Auth::user()->rol .'" no tiene permisos para entrar a esta sección.');
        }

        return $next($request);
    }
}
