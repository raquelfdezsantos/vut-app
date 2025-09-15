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

        // Reforzar capacidad (por si se cambia el max en el request)
        if ((int)$data['guests'] > (int)$property->capacity) {
            return back()->withErrors(['guests' => "Máximo {$property->capacity} huéspedes."])->withInput();
        }

        // Periodo de noches (excluye la fecha de salida)
        $period = CarbonPeriod::create($data['check_in'], $data['check_out'])->excludeEndDate();
        $dates  = collect($period)->map(fn($d) => $d->toDateString());

        if ($dates->isEmpty()) {
            // Esto cubre check_out == check_in si por cualquier motivo no lo paró el FormRequest
            return back()->withErrors(['check_in' => 'La fecha de salida debe ser posterior a la de entrada.'])->withInput();
        }

        // Valida estancia mínima ANTES de mirar solapes/tarifas ---
        $nights = $dates->count();
        $minStayGlobal = 2;
        if ($nights < $minStayGlobal) {
            return back()->withErrors([
                'check_in' => "La estancia mínima es de {$minStayGlobal} noches."
            ])->withInput();
        }

        // Solape con reservas existentes (no canceladas)
        $overlap = Reservation::where('property_id', $property->id)
            ->where(function ($q) use ($data) {
                // Tres condiciones de solape
                $q->whereBetween('check_in', [$data['check_in'], $data['check_out']])
                    ->orWhereBetween('check_out', [$data['check_in'], $data['check_out']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('check_in', '<=', $data['check_in'])
                            ->where('check_out', '>=', $data['check_out']);
                    });
            })
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        // Si hay solape, error
        if ($overlap) {
            return back()->withErrors(['check_in' => 'Las fechas seleccionadas no están disponibles.'])->withInput();
        }

        // Calendario de tarifas para cada noche del rango
        $rates = RateCalendar::where('property_id', $property->id)
            ->whereIn('date', $dates->all())
            ->get()
            ->keyBy('date');

        // Si no hay tarifa o no está disponible alguna noche, error
        foreach ($dates as $d) {
            $rate = $rates->get($d);
            if (!$rate || !$rate->is_available) {
                return back()->withErrors(['check_in' => 'No hay disponibilidad en alguna de las noches seleccionadas.'])->withInput();
            }
        }

        // Para respetar estancia mínima ANTES de crear la reserva
        $minStayFromRates = $rates->pluck('min_stay')->filter()->min();
        if ($minStayFromRates && $nights < $minStayFromRates) {
            return back()->withErrors([
                'check_in' => "La estancia mínima para esas fechas es de {$minStayFromRates} noches."
            ])->withInput();
        }

        // Precio total (sumatorio por noche)
        $total = $rates->sum('price');

        // Crear reserva
        $reservation = DB::transaction(function () use ($data, $property, $total) {
            return Reservation::create([
                'user_id'     => Auth::id(),
                'property_id' => $property->id,
                'check_in'    => $data['check_in'],
                'check_out'   => $data['check_out'],
                'guests'      => $data['guests'],
                'status'      => 'pending',
                'total_price' => $total,
            ]);
        });

        return redirect()->route('customer.bookings')->with('status', 'Reserva creada. Total: ' . $total . ' €');
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
