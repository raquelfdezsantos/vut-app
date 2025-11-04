<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Reservation.
 *
 * Representa una reserva realizada por un usuario sobre una propiedad.
 * Contiene información sobre fechas, huéspedes, precio total y estado.
 * 
 * @property \Illuminate\Support\Carbon $check_in
 * @property \Illuminate\Support\Carbon $check_out
 * @property int $user_id
 * @property int $property_id
 * @property int $guests
 * @property string $status
 * @property float $total_price
 *
 * @mixin \Eloquent
 */
class Reservation extends Model
{
    use HasFactory;
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

    /**
     * Relación: una reserva tiene una factura.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<Invoice>
     */
    public function invoice()
    {
        return $this->hasOne(\App\Models\Invoice::class);
    }

    /**
     * Relación: una reserva puede tener múltiples pagos.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Payment>
     */
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }


    /**
     * Total realmente pagado (succeeded) menos refunds.
     */
    public function paidAmount(): float
    {
        // Si la relación está cargada, usarla; si no, hacer query
        if ($this->relationLoaded('payments')) {
            $paid = $this->payments->where('status', 'succeeded')->sum('amount');
            $refunded = $this->payments->where('status', 'refunded')->sum('amount');
            return (float)($paid + $refunded); // refunds son negativos
        }
        
        $paid = (float) $this->payments()->where('status', 'succeeded')->sum('amount');
        $refunded = (float) $this->payments()->where('status', 'refunded')->sum('amount');
        return $paid + $refunded; // refunds son negativos
    }

    public function refundedAmount(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->where('status', 'refunded')->sum('amount');
        }
        
        return (float) $this->payments()
            ->where('status', 'refunded')
            ->sum('amount');
    }

    public function balanceDue(): float
    {
        // Lo que falta por cobrar si total > pagado; 0 si no.
        return max(0, (float)$this->total_price - $this->paidAmount());
    }

    public function overpaid(): float
    {
        // Exceso pagado (para detectar devoluciones); 0 si no.
        return max(0, $this->paidAmount() - (float)$this->total_price);
    }
}
