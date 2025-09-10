<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if ($request->user()) {
            // Si ya está logueado y entra a /login o /register, lo mandamos a home o dashboard
            return redirect()->intended('/');
        }

        return $next($request);
    }
}
