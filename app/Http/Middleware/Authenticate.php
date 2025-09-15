<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

/**
 * Middleware que verifica si el usuario está autenticado.
 */
class Authenticate extends Middleware
{
    /**
     * Redirige al login si no está autenticado.
     * 
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            return route('login'); // Breeze crea la ruta 'login'
        }

        return null;
    }
}
