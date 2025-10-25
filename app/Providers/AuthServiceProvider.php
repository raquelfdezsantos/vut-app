<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Policies\ReservationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Reservation::class => ReservationPolicy::class,
    ];

    public function boot(): void
    {
        // Si más adelante se definen Gates personalizados, irían aquí.
    }
}

