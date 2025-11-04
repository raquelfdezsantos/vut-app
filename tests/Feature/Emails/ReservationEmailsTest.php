<?php

use App\Models\{User, Property, RateCalendar};
use Illuminate\Support\Facades\Mail;
use App\Mail\{ReservationConfirmedMail, AdminNewReservationMail};
use function Pest\Laravel\{actingAs, post};

it('al crear reserva envía email al cliente y al admin', function () {
    Mail::fake();

    $user = User::factory()->create(['role' => 'customer']);
    $prop = Property::factory()->create(['capacity' => 4]);

    $checkIn = now()->addDays(10)->startOfDay();
    $checkOut = now()->addDays(12)->startOfDay();

    // NO crear fechas manualmente, dejar que el fallback del controlador las cree
    // Esto simula mejor el comportamiento real de producción

    actingAs($user);
    post(route('reservas.store'), [
        'property_id' => $prop->id,
        'check_in'    => $checkIn->toDateString(),
        'check_out'   => $checkOut->toDateString(),
        'guests'      => 2,
    ])->assertRedirect()->assertSessionHasNoErrors();

    Mail::assertSent(ReservationConfirmedMail::class, 1);
    Mail::assertSent(AdminNewReservationMail::class, 1);
});
