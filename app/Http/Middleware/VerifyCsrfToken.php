<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * Middleware que verifica los tokens CSRF en las solicitudes entrantes.
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * Las URIs que deberían estar exentas del verificador CSRF.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
