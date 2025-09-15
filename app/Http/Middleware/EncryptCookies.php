<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * Middleware para encriptar cookies.
 */
class EncryptCookies extends Middleware
{
    /**
     * Las cookies que no deben ser encriptadas.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
