<?php

use App\Models\{User, Property, Reservation, RateCalendar};
use Illuminate\Support\Facades\Mail;
use App\Mail\{PaymentReceiptMail, AdminPaymentNotificationMail};
use function Pest\Laravel\{actingAs, post};

it('al pagar envía email al cliente y al admin con la factura', function () {
    Mail::fake();

    $user = User::factory()->create(['role' => 'customer']);
    $prop = Property::factory()->create();

    foreach ([10,11] as $d) {
        RateCalendar::factory()->create([
            'property_id'  => $prop->id,
            'date'         => now()->addDays($d)->toDateString(),
            'price'        => 100,
            'is_available' => true,
            'min_stay'     => 2,
        ]);
    }

    $res = Reservation::factory()->create([
        'user_id' => $user->id,
        'property_id' => $prop->id,
        'check_in'  => now()->addDays(10),
        'check_out' => now()->addDays(12),
        'status'    => 'pending',
        'total_price' => 200,
    ]);

    actingAs($user);
    post(route('reservations.pay', ['id' => $res->id]))->assertRedirect();

    Mail::assertQueued(PaymentReceiptMail::class, 1);
    Mail::assertQueued(AdminPaymentNotificationMail::class, 1);
});
