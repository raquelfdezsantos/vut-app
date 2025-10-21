<?php 

use App\Models\{User, Property, Reservation, RateCalendar, Invoice, Payment};
use function Pest\Laravel\{actingAs, post, assertDatabaseHas};

it('pagar genera payment e invoice y pasa a paid', function () {
    $user = User::factory()->create();
    $prop = Property::factory()->create();

    // Fechas 10→12 (2 noches)
    foreach ([10,11] as $d) {
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
        'check_in' => now()->addDays(10),
        'check_out' => now()->addDays(12),
        'status' => 'pending',
        'total_price' => 200,
    ]);

    actingAs($user);
    post(route('reservations.pay', ['id' => $res->id]))->assertRedirect();

    assertDatabaseHas('reservations', ['id' => $res->id, 'status' => 'paid']);
    assertDatabaseHas('payments', ['reservation_id' => $res->id, 'amount' => 200.00, 'status' => 'succeeded']);
    assertDatabaseHas('invoices', ['reservation_id' => $res->id]);
});
