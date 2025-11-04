<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(Tests\TestCase::class, RefreshDatabase::class)->in(__DIR__);

uses(RefreshDatabase::class)->in('Feature');

// Evitar envíos reales durante los tests
beforeEach(function () {
	Mail::fake();
    Notification::fake();
    Event::fake();
});
