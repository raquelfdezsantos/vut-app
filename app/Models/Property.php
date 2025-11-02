<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Property.
 *
 * Representa una vivienda turística registrada en el sistema.
 * Cada propiedad tiene fotos, un calendario de tarifas y reservas asociadas.
 */
class Property extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Campos asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'city',
        'postal_code',
        'province',
        'capacity',
        'tourism_license',
        'rental_registration',
        'latitude',
        'longitude',
    ];

    /**
     * Relación: una propiedad puede tener muchas fotos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(\App\Models\Photo::class);
    }

    /**
     * Relación: una propiedad tiene un calendario de tarifas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rateCalendar()
    {
        return $this->hasMany(RateCalendar::class, 'property_id');
    }

    /**
     * Relación: una propiedad puede tener muchas reservas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany(\App\Models\Reservation::class);
    }
}
