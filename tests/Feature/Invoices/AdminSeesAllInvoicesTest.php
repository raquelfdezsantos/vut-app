<?php

use App\Models\{User, Property, Reservation, Invoice};
use function Pest\Laravel\{actingAs, get};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin ve todas las facturas de todos los usuarios', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $prop = Property::factory()->create();

    // Crear 2 reservas y facturas de usuarios diferentes
    $resA = Reservation::factory()->create([
        'user_id' => $userA->id,
        'property_id' => $prop->id,
        'status' => 'paid',
        'total_price' => 100,
    ]);
    $invoiceA = Invoice::factory()->create([
        'reservation_id' => $resA->id,
        'number' => 'INV-001',
        'amount' => 100,
        'issued_at' => now(),
    ]);

    $resB = Reservation::factory()->create([
        'user_id' => $userB->id,
        'property_id' => $prop->id,
        'status' => 'paid',
        'total_price' => 200,
    ]);
    $invoiceB = Invoice::factory()->create([
        'reservation_id' => $resB->id,
        'number' => 'INV-002',
        'amount' => 200,
        'issued_at' => now(),
    ]);

    // Admin debe ver ambas facturas
    $response = actingAs($admin)->get(route('admin.invoices.index'));

    $response->assertOk();
    $response->assertSee('INV-001');
    $response->assertSee('INV-002');
});

it('cliente no puede acceder a la vista admin de facturas', function () {
    $user = User::factory()->create(); // cliente normal

    $response = actingAs($user)->get(route('admin.invoices.index'));

    $response->assertForbidden();
});
