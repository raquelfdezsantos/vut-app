<?php

use App\Models\{User, Property, Reservation, RateCalendar, Payment};
use function Pest\Laravel\{actingAs, put, assertDatabaseHas};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('editar aumenta total y deja balance pendiente si era paid', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $prop  = Property::factory()->create();

    // Calendario: 3 noches libres a 100
    foreach ([10,11,12] as $d) {
        RateCalendar::factory()->create([
            'property_id' => $prop->id,
            'date' => now()->addDays($d)->toDateString(),
            'price' => 100,
            'is_available' => true,
            'min_stay' => 2,
        ]);
    }

    // Reserva pagada 10→12 (2 noches × 100 × 1 pax = 200)
    $res = Reservation::factory()->create([
        'property_id' => $prop->id,
        'status' => 'paid',
        'check_in' => now()->addDays(10),
        'check_out'=> now()->addDays(12),
        'guests' => 1,
        'total_price' => 200,
    ]);
    Payment::factory()->create([
        'reservation_id' => $res->id,
        'amount' => 200,
        'status' => 'succeeded',
        'method' => 'stripe',
    ]);

    actingAs($admin);
    // Editar a 10→13 (3 noches × 100 × 1 pax = 300)
    put(route('admin.reservations.update', $res->id), [
        'check_in' => now()->addDays(10)->toDateString(),
        'check_out'=> now()->addDays(13)->toDateString(),
        'guests' => 1,
    ])->assertRedirect();

    $res->refresh();
    expect($res->total_price)->toBe(300);
    expect($res->paidAmount())->toBe(200.0);
    expect($res->balanceDue())->toBe(100.0);
});
