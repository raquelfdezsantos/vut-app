<?php

use App\Models\{Reservation, Payment};

it('balanceDue y paidAmount se calculan correctamente', function () {
    $r = new Reservation(['total_price' => 300]);

    // simular relación en memoria
    $r->setRelation('payments', collect([
        new Payment(['amount' => 200, 'status' => 'succeeded']),
        new Payment(['amount' => -50,  'status' => 'refunded']),
    ]));

    expect($r->paidAmount())->toBe(150.0);
    expect($r->balanceDue())->toBe(150.0);
});
