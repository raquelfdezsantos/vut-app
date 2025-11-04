<?php

use App\Models\{User, Property, Reservation, Invoice};
use function Pest\Laravel\{actingAs, get};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('el listado de mis facturas solo muestra facturas de mi usuario', function () {
    $a = User::factory()->create(['role' => 'customer']);
    $b = User::factory()->create(['role' => 'customer']);
    $prop = Property::factory()->create();

    // Factura de A
    $resA = Reservation::factory()->create([
        'user_id' => $a->id, 'property_id' => $prop->id, 'status' => 'paid', 'total_price' => 111,
    ]);
    $invA = Invoice::factory()->create([
        'reservation_id' => $resA->id, 
        'number' => 'INV-A-00001', 
        'amount' => 111,
        'issued_at' => now(),
    ]);

    // Factura de B
    $resB = Reservation::factory()->create([
        'user_id' => $b->id, 'property_id' => $prop->id, 'status' => 'paid', 'total_price' => 222,
    ]);
    $invB = Invoice::factory()->create([
        'reservation_id' => $resB->id, 
        'number' => 'INV-B-00001', 
        'amount' => 222,
        'issued_at' => now(),
    ]);

    actingAs($a);

    // La ruta es 'invoices.index' para clientes (GET /mis-facturas)
    $resp = get(route('invoices.index'));

    $resp->assertOk()
         ->assertSee('INV-A-00001')
         ->assertDontSee('INV-B-00001');
});
