<?php

use App\Models\{User, Property, Reservation, Invoice};
use function Pest\Laravel\{actingAs, get};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('el dueño de la reserva puede ver su factura', function () {
    $u = User::factory()->create(['role' => 'customer']);
    $prop = Property::factory()->create();
    $res = Reservation::factory()->create([
        'user_id' => $u->id,
        'property_id' => $prop->id,
        'status' => 'paid',
        'total_price' => 123.45,
    ]);
    $inv = Invoice::factory()->create([
        'reservation_id' => $res->id,
        'number' => 'INV-OK-00001',
        'amount' => 123.45,
        'issued_at' => now(),
    ]);

    actingAs($u);
    get(route('invoices.show', $inv->number))
        ->assertOk()
        ->assertSee('INV-OK-00001');
});
