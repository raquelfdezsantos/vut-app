<?php

use App\Models\{User, Property, RateCalendar};
use Carbon\Carbon;
use function Pest\Laravel\{actingAs, post};

it('no permite reservar por debajo de min_stay', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $property = Property::factory()->create(['capacity' => 4]);

    // 1 noche, pero el min_stay real será 2 por RateCalendar
    $checkIn = Carbon::now()->addDays(10)->toDateString();
    $checkOut = Carbon::now()->addDays(11)->toDateString();

    // Sembrar rate calendar con min_stay=2
    RateCalendar::factory()->create([
        'property_id' => $property->id,
        'date' => Carbon::parse($checkIn)->toDateString(),
        'price' => 100,
        'is_available' => true,
        'min_stay' => 2,
    ]);

    actingAs($user);

    $response = post(route('reservas.store'), [
        'property_id' => $property->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'guests' => 2,
    ]);
    $response->assertSessionHasErrors('check_in');
});
