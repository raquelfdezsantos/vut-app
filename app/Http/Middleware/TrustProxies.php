<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * Middleware que configura los proxies confiables para la aplicación.
 */
class TrustProxies extends Middleware
{
    /**
     * Las direcciones IP de los proxies confiables.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * Las cabeceras que deben ser utilizadas para detectar proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
