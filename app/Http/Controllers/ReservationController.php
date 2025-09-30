<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\RateCalendar;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Muestra la ficha de una propiedad y su formulario de reserva.
     *
     * @param  string  $slug  Slug de la propiedad
     * @return \Illuminate\Contracts\View\View
     */
    public function create(string $slug)
    {
        $property = Property::with('photos')->where('slug', $slug)->firstOrFail();
        return view('property.show', compact('property'));
    }


    /** LISTADO de reservas del cliente */
    public function index()
    {
        $reservations = Reservation::with('property')
            ->where('user_id', Auth::id())
            ->latest('check_in')
            ->paginate(10);

        // Usa la vista que tienes en carpeta:
        return view('customer.bookings.index', compact('reservations'));
        // Si prefieres la otra:
        // return view('customer.bookings', compact('reservations'));
    }


    /**
     * Crea una reserva validando reglas de negocio y calculando el precio:
     * - Capacidad máxima del alojamiento
     * - Rango de fechas válido y estancia mínima
     * - Ausencia de solapamientos con reservas existentes
     * - Disponibilidad diaria en calendario de tarifas
     * - Cálculo del total sumando el precio de cada noche
     *
     * @param  \App\Http\Requests\StoreReservationRequest  $request  Datos validados de la reserva
     * @return \Illuminate\Http\RedirectResponse  Redirige al listado de reservas del cliente
     */
    public function store(StoreReservationRequest $request)
    {
        $data = $request->validated();

        $property = Property::findOrFail($data['property_id']);

        if ((int)$data['guests'] > (int)$property->capacity) {
            return back()->withErrors(['guests' => "Máximo {$property->capacity} huéspedes."])->withInput();
        }

        $period = CarbonPeriod::create($data['check_in'], $data['check_out'])->excludeEndDate();
        $dates  = collect($period)->map(fn($d) => $d->toDateString());

        if ($dates->isEmpty()) {
            return back()->withErrors(['check_in' => 'La fecha de salida debe ser posterior a la de entrada.'])->withInput();
        }

        $nights = $dates->count();
        $minStayGlobal = 2;
        if ($nights < $minStayGlobal) {
            return back()->withErrors(['check_in' => "La estancia mínima es de {$minStayGlobal} noches."])->withInput();
        }

        $overlap = Reservation::where('property_id', $property->id)
            ->where(function ($q) use ($data) {
                $q->whereBetween('check_in', [$data['check_in'], $data['check_out']])
                  ->orWhereBetween('check_out', [$data['check_in'], $data['check_out']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('check_in', '<=', $data['check_in'])
                         ->where('check_out', '>=', $data['check_out']);
                  });
            })
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($overlap) {
            return back()->withErrors(['check_in' => 'Las fechas seleccionadas no están disponibles.'])->withInput();
        }

        $rates = RateCalendar::where('property_id', $property->id)
            ->whereIn('date', $dates->all())
            ->get()
            ->keyBy('date');

        foreach ($dates as $d) {
            $rate = $rates->get($d);
            if (!$rate || !$rate->is_available) {
                return back()->withErrors(['check_in' => 'No hay disponibilidad en alguna de las noches seleccionadas.'])->withInput();
            }
        }

        $minStayFromRates = $rates->pluck('min_stay')->filter()->min();
        if ($minStayFromRates && $nights < $minStayFromRates) {
            return back()->withErrors(['check_in' => "La estancia mínima para esas fechas es de {$minStayFromRates} noches."])->withInput();
        }

        // Ojo: tu modelo parece usar 'price' en RateCalendar. Si fuera 'price_per_night', cambia aquí.
        $total = $rates->sum('price');

        DB::transaction(function () use ($data, $property, $total) {
            Reservation::create([
                'user_id'     => Auth::id(),
                'property_id' => $property->id,
                'check_in'    => $data['check_in'],
                'check_out'   => $data['check_out'],
                'guests'      => $data['guests'],
                'status'      => 'pending',
                'total_price' => $total,
            ]);
        });

        // Redirige a la ruta en español (ver rutas abajo)
        return redirect()->route('reservas.index')
            ->with('status', 'Reserva creada. Total: ' . number_format($total, 2, ',', '.') . ' €');
    }


    /**
     * Lista las reservas del cliente autenticado (con paginación).
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function myBookings()
    {
        $reservations = Reservation::with('property')
            ->where('user_id', Auth::id())
            ->latest('check_in')
            ->paginate(10);

        return view('customer.bookings', compact('reservations'));
    }



}
