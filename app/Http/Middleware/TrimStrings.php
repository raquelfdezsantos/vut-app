<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

/**
 * Middleware que recorta los espacios en blanco de los campos de entrada, excepto para ciertos campos.
 */
class TrimStrings extends Middleware
{
    /**
     * Nombres de los campos que no deben ser recortados.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
