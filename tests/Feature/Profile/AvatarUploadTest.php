<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{actingAs, patch, assertDatabaseHas};

it('sube avatar al disco public y actualiza el usuario', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    actingAs($user);

    $file = UploadedFile::fake()->image('avatar.jpg', 300, 300);

    // Ajusta si tu campo es 'avatar' y tu ruta es 'profile.update'
    $resp = patch(route('profile.update'), [
        'name'   => $user->name,
        'email'  => $user->email,
        'avatar' => $file,
    ]);

    $resp->assertRedirect();

    $user->refresh();
    expect($user->avatar_path)->not()->toBeNull();

    // se guardó en public/...
    Storage::disk('public')->assertExists($user->avatar_path);

    assertDatabaseHas('users', [
        'id'          => $user->id,
        'avatar_path' => $user->avatar_path,
    ]);
});
