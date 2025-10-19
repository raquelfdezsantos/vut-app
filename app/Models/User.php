<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo User.
 *
 * Representa a los usuarios del sistema, que pueden ser clientes o administradores.
 * Gestiona la autenticación, notificaciones y relaciones con sus reservas.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atributos que pueden asignarse masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar_path',
    ];

    /**
     * Atributos ocultos al serializar el modelo.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversión automática de atributos a tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
