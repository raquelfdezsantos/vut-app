<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que redirige a los usuarios autenticados.
 */
class RedirectIfAuthenticated
{
    /**
     * Si ya está logueado y entra a /login o /register, lo mando a home o dashboard
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string[] $guards
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if ($user = $request->user()) {
        // Para diferenciar por rol:
        return $user->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('customer.bookings'); 
    }

    return $next($request);
    }
}
