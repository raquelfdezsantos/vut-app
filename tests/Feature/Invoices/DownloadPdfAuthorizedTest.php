<?php

use App\Models\{User, Property, Reservation, Invoice};
use function Pest\Laravel\{actingAs, get};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('dueño puede ver su factura', function () {
    $user = User::factory()->create();
    $prop = Property::factory()->create();

    $reservation = Reservation::factory()->create([
        'user_id' => $user->id,
        'property_id' => $prop->id,
        'status' => 'paid',
        'total_price' => 150,
    ]);

    $invoice = Invoice::factory()->create([
        'reservation_id' => $reservation->id,
        'number' => 'INV-TEST-001',
        'amount' => 150,
        'issued_at' => now(),
    ]);

    $response = actingAs($user)->get(route('invoices.show', $invoice->number));

    $response->assertOk();
});

it('admin puede ver cualquier factura', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $prop = Property::factory()->create();

    $reservation = Reservation::factory()->create([
        'user_id' => $user->id,
        'property_id' => $prop->id,
        'status' => 'paid',
        'total_price' => 150,
    ]);

    $invoice = Invoice::factory()->create([
        'reservation_id' => $reservation->id,
        'number' => 'INV-TEST-002',
        'amount' => 150,
        'issued_at' => now(),
    ]);

    $response = actingAs($admin)->get(route('invoices.show', $invoice->number));

    $response->assertOk();
});

it('usuario no autorizado recibe 403 al intentar descargar factura ajena', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $prop = Property::factory()->create();

    $reservation = Reservation::factory()->create([
        'user_id' => $userA->id,
        'property_id' => $prop->id,
        'status' => 'paid',
        'total_price' => 150,
    ]);

    $invoice = Invoice::factory()->create([
        'reservation_id' => $reservation->id,
        'number' => 'INV-TEST-003',
        'amount' => 150,
        'issued_at' => now(),
    ]);

    // UserB intenta descargar la factura de UserA
    $response = actingAs($userB)->get(route('invoices.show', $invoice->number));

    $response->assertForbidden();
});
