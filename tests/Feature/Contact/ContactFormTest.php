<?php

use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;
use function Pest\Laravel\{post};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('envía email de contacto con datos válidos', function () {
    Mail::fake();

    post(route('contact.store'), [
        'name' => 'Juan Pérez',
        'email' => 'juan@example.com',
        'message' => 'Me gustaría saber más sobre las tarifas.',
    ])->assertRedirect();

    Mail::assertQueued(ContactMessageMail::class, function ($mail) {
        return $mail->hasTo(env('MAIL_ADMIN', 'admin@vut.test'));
    });
});

it('bloquea después de 6 intentos (rate limit)', function () {
    for ($i = 0; $i < 5; $i++) {
        post(route('contact.store'), [
            'name' => "Usuario $i",
            'email' => "user$i@example.com",
            'message' => 'Mensaje de prueba',
        ])->assertStatus(302); // Debería redirigir correctamente
    }

    // El 6º intento debería ser bloqueado
    post(route('contact.store'), [
        'name' => 'Usuario 6',
        'email' => 'user6@example.com',
        'message' => 'Este debería fallar',
    ])->assertStatus(429); // Too Many Requests
});
