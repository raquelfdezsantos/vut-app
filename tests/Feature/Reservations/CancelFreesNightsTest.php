<?php 

use App\Models\{User, Property, Reservation, RateCalendar};
use function Pest\Laravel\{actingAs, post, assertDatabaseHas};

it('cancelar libera noches (is_available=true)', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $prop = Property::factory()->create();

    // 10→13 (10,11,12 bloqueadas en la vida real)
    foreach ([10,11,12] as $d) {
        RateCalendar::factory()->create([
            'property_id' => $prop->id,
            'date' => now()->addDays($d)->toDateString(),
            'price' => 100,
            'is_available' => false,
            'min_stay' => 2,
        ]);
    }

    $res = Reservation::factory()->create([
        'property_id' => $prop->id,
        'status' => 'pending',
        'check_in' => now()->addDays(10),
        'check_out' => now()->addDays(13),
        'total_price' => 300,
    ]);

    actingAs($admin);
    post(route('admin.reservations.cancel', $res->id))->assertRedirect();

    foreach ([10,11,12] as $d) {
        assertDatabaseHas('rate_calendars', [
            'property_id' => $prop->id,
            'date' => now()->addDays($d)->toDateString(),
            'is_available' => true,
        ]);
    }
});
