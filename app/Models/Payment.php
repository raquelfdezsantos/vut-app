<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Payment.
 *
 * Gestiona los pagos simulados dentro del sistema.
 * Cada pago está vinculado a una reserva y genera una factura correspondiente.
 * 
 * @property int $reservation_id
 * @property float $amount
 * @property string $method
 * @property string $status
 * @property string $provider_ref
 */
class Payment extends Model
{
    /**
     * Atributos que pueden asignarse de forma masiva.
     * 
     * @var array <int, string>
     */
    protected $fillable = [
        'reservation_id',
        'amount',
        'method',
        'status',
        'provider_ref',
    ];

    /**
     * Relación: un pago pertenece a una reserva.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Reservation, Payment>
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}