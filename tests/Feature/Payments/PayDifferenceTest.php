<?php

use App\Models\{User, Property, Reservation, RateCalendar, Payment};
use function Pest\Laravel\{actingAs, post, assertDatabaseHas};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('payDifference crea un Payment por el balance pendiente', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $prop = Property::factory()->create();

    foreach ([10,11,12] as $d) {
        RateCalendar::factory()->create([
            'property_id' => $prop->id,
            'date' => now()->addDays($d)->toDateString(),
            'price' => 100,
            'is_available' => true,
            'min_stay' => 2,
        ]);
    }

    $res = Reservation::factory()->create([
        'user_id' => $user->id,
        'property_id' => $prop->id,
        'status' => 'paid',
        'check_in' => now()->addDays(10),
        'check_out'=> now()->addDays(13), // 3 noches = 300
        'total_price' => 300,
    ]);

    // Ya había pagado 200 (falta 100)
    Payment::factory()->create([
        'reservation_id' => $res->id,
        'amount' => 200,
        'status' => 'succeeded',
        'method' => 'stripe',
    ]);

    actingAs($user);
    post(route('reservations.pay_difference', $res->id))->assertRedirect();

    assertDatabaseHas('payments', [
        'reservation_id' => $res->id,
        'amount' => 100.00,
        'status' => 'succeeded',
    ]);
});
