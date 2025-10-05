<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

/**
 * Controlador de propiedades.
 *
 * Gestiona la visualización pública del alojamiento y sus detalles:
 * fotos, precios, calendario de disponibilidad y ficha completa.
 */
class PropertyController extends Controller
{
    /**
     * Muestra el listado de propiedades disponibles.
     *
     * Obtiene las propiedades junto con sus fotos asociadas,
     * ordenadas de la más reciente a la más antigua, y las pagina.
     *
     * @return \Illuminate\Contracts\View\View Vista con el listado de propiedades.
     */
    public function index()
    {
        $properties = Property::with('photos')->latest()->paginate(6);
        return view('properties.index', compact('properties'));
    }

    /**
     * Muestra la ficha detallada de una propiedad.
     *
     * Carga las fotos y el calendario de tarifas disponibles
     * (solo fechas futuras con disponibilidad).
     *
     * @param \App\Models\Property $property Propiedad seleccionada.
     * @return \Illuminate\Contracts\View\View Vista con los detalles de la propiedad.
     */
    public function show(Property $property)
    {
        $property->load([
            'photos',
            'rateCalendar' => function ($q) {
                $q->where('is_available', true)
                  ->whereDate('date', '>=', now()->toDateString())
                  ->orderBy('date');
            },
        ]);

        $fromPrice = optional($property->rateCalendar->first())->price ?? null;

        return view('property.show', compact('property', 'fromPrice'));
    }
}
