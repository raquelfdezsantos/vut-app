<?php 

use App\Models\{User, Property, Reservation, RateCalendar};
use function Pest\Laravel\{actingAs, post};

it('rechaza solapes de [check_in, check_out)', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $prop = Property::factory()->create(['capacity' => 4]); // Capacidad suficiente

    // Reserva existente 10→13
    $existing = Reservation::factory()->create([
        'user_id' => $user->id,
        'property_id' => $prop->id,
        'check_in' => now()->addDays(10),
        'check_out' => now()->addDays(13),
        'status' => 'pending',
        'total_price' => 300,
    ]);

    // Sembrar rate calendar libre
    foreach (range(10,15) as $d) {
        RateCalendar::factory()->create([
            'property_id' => $prop->id,
            'date' => now()->addDays($d)->toDateString(),
            'price' => 100,
            'is_available' => true,
            'min_stay' => 2,
        ]);
    }

    // Intentar nueva que solapa 12→14
    actingAs($user);
    $response = post(route('reservas.store'), [
        'property_id' => $prop->id,
        'check_in' => now()->addDays(12)->toDateString(),
        'check_out' => now()->addDays(14)->toDateString(),
        'guests' => 2,
    ]);

    $response->assertSessionHasErrors('check_in');
});
