<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

/**
 * Middleware que previene solicitudes durante el modo de mantenimiento.
 */
class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * Durante el modo de mantenimiento, las URIs que deberían ser accesibles.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
