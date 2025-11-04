<?php

use App\Models\{User, Property, Reservation, RateCalendar};
use function Pest\Laravel\{actingAs, post, assertDatabaseHas};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin puede cancelar reserva y libera fechas', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $prop = Property::factory()->create();

    // Crear calendario con fechas disponibles
    foreach ([10, 11, 12] as $d) {
        RateCalendar::factory()->create([
            'property_id' => $prop->id,
            'date' => now()->addDays($d)->toDateString(),
            'price' => 100,
            'is_available' => true,
        ]);
    }

    $reservation = Reservation::factory()->create([
        'user_id' => $user->id,
        'property_id' => $prop->id,
        'check_in' => now()->addDays(10),
        'check_out' => now()->addDays(13),
        'status' => 'pending',
        'total_price' => 300,
    ]);

    // Admin cancela la reserva
    $response = actingAs($admin)->post(route('admin.reservations.cancel', $reservation->id));

    $response->assertRedirect();

    // Verificar que la reserva está cancelada
    $reservation->refresh();
    expect($reservation->status)->toBe('cancelled');

    // Verificar que las fechas están liberadas (is_available = true)
    $freedDates = RateCalendar::where('property_id', $prop->id)
        ->whereDate('date', '>=', now()->addDays(10)->toDateString())
        ->whereDate('date', '<', now()->addDays(13)->toDateString())
        ->get();

    foreach ($freedDates as $date) {
        expect($date->is_available)->toBeTrue();
    }
});

it('admin no puede cancelar reserva ya pagada', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $prop = Property::factory()->create();

    $reservation = Reservation::factory()->create([
        'user_id' => $user->id,
        'property_id' => $prop->id,
        'status' => 'paid',
        'total_price' => 300,
    ]);

    $response = actingAs($admin)->post(route('admin.reservations.cancel', $reservation->id));

    // Debería redirigir con mensaje de error
    $response->assertRedirect();
    $response->assertSessionHas('error');

    // La reserva sigue como 'paid'
    $reservation->refresh();
    expect($reservation->status)->toBe('paid');
});
