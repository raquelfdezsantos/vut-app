<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Photo.
 *
 * Gestiona las fotografías asociadas a una propiedad.
 * Cada foto pertenece a una única propiedad y contiene su ruta de almacenamiento.
 */
class Photo extends Model
{
    protected $fillable = [
        'property_id',
        'url',
        'is_cover',
        'sort_order',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    /**
     * Relación: una foto pertenece a una propiedad.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
