<?php

use App\Models\{User, Property, RateCalendar};
use function Pest\Laravel\{actingAs, post};

it('rechaza reservas por encima de la capacidad', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $prop = Property::factory()->create(['capacity' => 2]);

    foreach ([10,11] as $d) {
        RateCalendar::factory()->create([
            'property_id'  => $prop->id,
            'date'         => now()->addDays($d)->toDateString(),
            'price'        => 100,
            'is_available' => true,
            'min_stay'     => 2,
        ]);
    }

    actingAs($user);
    $resp = post(route('reservas.store'), [
        'property_id' => $prop->id,
        'check_in'    => now()->addDays(10)->toDateString(),
        'check_out'   => now()->addDays(12)->toDateString(),
        'guests'      => 4, // > capacidad
    ]);

    $resp->assertSessionHasErrors('guests');
});
