<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\RateCalendar;
use App\Models\Reservation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Controlador de propiedades.
 *
 * Gestiona la visualización pública del alojamiento y sus detalles:
 * fotos, precios, calendario de disponibilidad y ficha completa.
 */
class PropertyController extends Controller
{
    /**
     * Muestra la HOME adaptativa según el número de propiedades.
     * 
     * - Si hay 1 propiedad: muestra ficha completa
     * - Si hay 2+: muestra grid de propiedades
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function home()
    {
        $totalProperties = Property::whereNull('deleted_at')->count();

        if ($totalProperties === 0) {
            abort(404, 'No hay propiedades disponibles');
        }

        if ($totalProperties === 1) {
            // Solo hay 1 propiedad: mostrar ficha completa
            $property = Property::with(['photos', 'rateCalendar'])
                ->whereNull('deleted_at')
                ->firstOrFail();

            return view('home-single', compact('property', 'totalProperties'));
        }

        // Hay múltiples propiedades: mostrar grid
        $properties = Property::with('photos')
            ->whereNull('deleted_at')
            ->latest()
            ->get();

        return view('home-multi', compact('properties', 'totalProperties'));
    }

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

        // Cargar fechas bloqueadas SOLO desde reservas activas
        // Bloquear las NOCHES ocupadas: [check_in, check_out) - excluye el día de check-out
        $reservations = Reservation::where('property_id', $property->id)
            ->whereNotIn('status', ['cancelled'])
            ->whereDate('check_out', '>', now()->toDateString())
            ->get();

        $blockedDates = $reservations->flatMap(function ($reservation) {
            $period = CarbonPeriod::create($reservation->check_in, $reservation->check_out)->excludeEndDate();
            return collect($period)->map(fn($d) => $d->toDateString());
        })
            ->unique()
            ->values()
            ->toArray();

        // Días donde hay check-in (noche ocupada, aunque haya check-out también)
        $checkinDates = $reservations->map(fn($r) => Carbon::parse($r->check_in)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        // Días donde hay check-out (noche potencialmente libre)
        $checkoutDates = $reservations->map(fn($r) => Carbon::parse($r->check_out)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        return view('property.show', compact('property', 'fromPrice', 'blockedDates', 'checkinDates', 'checkoutDates'));
    }

    /**
     * Muestra la página de reservar con la primera propiedad disponible.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function reservar()
    {
        // si es una única propiedad demo:
        $property = Property::with('photos')->firstOrFail();

        // Fechas bloqueadas desde tu RateCalendar
        $blockedDates = RateCalendar::query()
            ->where('property_id', $property->id)
            ->where('is_available', false)
            ->orderBy('date')
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // Opcional: marcar días de entrada/salida reales de reservas
        $reservations = Reservation::where('property_id', $property->id)
            ->whereIn('status', ['pending', 'paid'])
            ->get();

        $checkinDates = $reservations->pluck('check_in')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->unique()->values()->toArray();
        $checkoutDates = $reservations->pluck('check_out')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->unique()->values()->toArray();

        // Precio “desde” (si lo usas)
        $fromPrice = RateCalendar::where('property_id', $property->id)
            ->where('is_available', true)
            ->min('price');

        return view('reservar.index', compact(
            'property',
            'fromPrice',
            'blockedDates',
            'checkinDates',
            'checkoutDates'
        ));
    }
}
