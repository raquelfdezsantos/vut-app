<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(Tests\TestCase::class, RefreshDatabase::class)->in(__DIR__);

// Evitar envíos reales de correo durante los tests
beforeEach(function () {
	Mail::fake();
    Notification::fake();
    Event::fake();
});
