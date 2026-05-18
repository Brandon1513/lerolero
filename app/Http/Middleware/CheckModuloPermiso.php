<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckModuloPermiso
{
    /**
     * Verifica que el usuario tenga permiso para el módulo actual.
     * Uso en rutas: middleware('permiso:dashboard.ver')
     */
    public function handle(Request $request, Closure $next, string $permiso): mixed
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Administrador siempre tiene acceso total
        if (auth()->user()->hasRole('administrador')) {
            return $next($request);
        }

        // Verificar permiso específico (rol + directo)
        if (!auth()->user()->can($permiso)) {
            abort(403, 'No tienes permiso para acceder a este módulo.');
        }

        return $next($request);
    }
}