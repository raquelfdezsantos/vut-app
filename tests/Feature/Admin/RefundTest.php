<?php

use App\Models\{User, Property, Reservation, Payment};
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentRefundIssuedMail;
use function Pest\Laravel\{actingAs, post, assertDatabaseHas};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin puede procesar refund completo y envía emails', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $prop = Property::factory()->create();

    $reservation = Reservation::factory()->create([
        'user_id' => $user->id,
        'property_id' => $prop->id,
        'check_in' => now()->addDays(10),
        'check_out' => now()->addDays(13),
        'status' => 'paid',
        'total_price' => 300,
    ]);

    // Pago inicial
    Payment::factory()->create([
        'reservation_id' => $reservation->id,
        'amount' => 300,
        'status' => 'succeeded',
        'method' => 'stripe',
    ]);

    // Admin procesa refund completo
    $response = actingAs($admin)->post(route('admin.reservations.refund', $reservation->id));

    $response->assertRedirect();

    // Verificar que se creó el payment de reembolso
    assertDatabaseHas('payments', [
        'reservation_id' => $reservation->id,
        'amount' => 300,
        'method' => 'simulated',
        'status' => 'refunded',
    ]);

    // Verificar que reserva está cancelada
    $reservation->refresh();
    expect($reservation->status)->toBe('cancelled');

    // Verificar que se enviaron emails
    Mail::assertSent(PaymentRefundIssuedMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('admin no puede procesar refund si no está paid', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $prop = Property::factory()->create();

    $reservation = Reservation::factory()->create([
        'user_id' => $user->id,
        'property_id' => $prop->id,
        'status' => 'pending',
        'total_price' => 300,
    ]);

    // Intentar refund de reserva pending
    $response = actingAs($admin)->post(route('admin.reservations.refund', $reservation->id));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    // No debe haberse creado el payment
    expect(Payment::where('reservation_id', $reservation->id)->count())->toBe(0);
});
