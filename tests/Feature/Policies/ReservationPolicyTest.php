<?php

use App\Models\{User, Property, Reservation};
use function Pest\Laravel\{actingAs, get};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('un cliente no puede ver la factura de otro', function () {
    $a = User::factory()->create(['role' => 'customer']);
    $b = User::factory()->create(['role' => 'customer']);
    $prop = Property::factory()->create();

    $res = Reservation::factory()->create([
        'user_id' => $a->id,
        'property_id' => $prop->id,
        'status' => 'paid',
        'total_price' => 100,
    ]);
    $inv = \App\Models\Invoice::factory()->create([
        'reservation_id' => $res->id,
        'number' => 'INV-TEST-00001',
        'amount' => 100,
        'issued_at' => now(),
    ]);

    actingAs($b);
    get(route('invoices.show', $inv->number))
        ->assertStatus(403); // o 404 según controlador
});
