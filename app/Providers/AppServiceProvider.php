<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\EnsureUserHasRole;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Registro explícito del alias 'role' por si el Kernel no se carga 
        $this->app['router']->aliasMiddleware('role', EnsureUserHasRole::class);
    }
}
