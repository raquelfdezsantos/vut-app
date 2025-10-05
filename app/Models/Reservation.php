<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Reservation.
 *
 * Representa una reserva realizada por un usuario sobre una propiedad.
 * Contiene información sobre fechas, huéspedes, precio total y estado.
 */
class Reservation extends Model
{
    /**
     * Atributos que pueden asignarse de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'property_id',
        'check_in',
        'check_out',
        'guests',
        'status',
        'total_price'
    ];

    /**
     * Conversión automática de atributos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in'  => 'date',
        'check_out' => 'date',
    ];

    /**
     * Relación: una reserva pertenece a una propiedad.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(\App\Models\Property::class);
    }

    /**
     * Relación: una reserva pertenece a un usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
