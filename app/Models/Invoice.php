<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Invoice.
 *
 * Representa la factura generada tras el pago simulado de una reserva.
 * Incluye número de factura, importe total, fecha de emisión y relación con la reserva.
 * 
 * @property int $id
 * @property int $reservation_id
 * @property string $number
 * @property string $pdf_path
 * @property \Illuminate\Support\Carbon $issued_at
 * @property float $amount
 */
class Invoice extends Model
{
    use HasFactory;
    /**
     * Resumen de los atributos que pueden asignarse de forma masiva.
     * 
     * @var array <int, string>
     */
    protected $fillable = [
        'reservation_id',
        'number',
        'pdf_path',
        'issued_at',
        'amount',
    ];

    /**
     * Conversión automática de atributos a tipos nativos.
     * 
     * @var array <int, string>
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'amount'    => 'decimal:2',
    ];

    /**
     * Relación: una factura pertenece a una reserva.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Reservation, Invoice>
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
