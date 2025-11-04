<?php

use App\Models\{User, Property, Reservation, RateCalendar};
use function Pest\Laravel\{actingAs, post};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite reserva que empieza justo el día de checkout de otra', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $prop = Property::factory()->create();

    // Reserva existente 10→12 (ocupa 10 y 11)
    Reservation::factory()->create([
        'user_id' => $user->id,
        'property_id' => $prop->id,
        'check_in' => now()->addDays(10),
        'check_out'=> now()->addDays(12),
        'status' => 'pending',
        'total_price' => 200,
    ]);

    // Calendario libre del 12→14 (12 y 13)
    foreach ([12,13] as $d) {
        RateCalendar::factory()->create([
            'property_id'   => $prop->id,
            'date'          => now()->addDays($d)->toDateString(),
            'price'         => 100,
            'is_available'  => true,
            'min_stay'      => 2,
        ]);
    }

    actingAs($user);
    // Crear 12→14 debe ser OK (ajusta si tu store es otra ruta)
    $resp = post(route('reservas.store'), [
        'property_id' => $prop->id,
        'check_in'    => now()->addDays(12)->toDateString(),
        'check_out'   => now()->addDays(14)->toDateString(),
        'guests'      => 2,
    ]);

    $resp->assertRedirect(); // llegó a mis-reservas sin error de solape
});
