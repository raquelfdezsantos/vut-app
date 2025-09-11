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
     * Mostrar detalle de propiedad + formulario de reserva.
     */
    public function create(string $slug)
    {
        $property = Property::with('photos')->where('slug', $slug)->firstOrFail();
        return view('property.show', compact('property'));
    }

    /**
     * Guardar la reserva (valida disponibilidad y calcula precio).
     */
    public function store(StoreReservationRequest $request)
    {
        $data = $request->validated();

        $property = Property::findOrFail($data['property_id']);

        // Reforzar capacidad (por si cambiaste el max del request)
        if ((int)$data['guests'] > (int)$property->capacity) {
            return back()->withErrors(['guests' => "Máximo {$property->capacity} huéspedes."])->withInput();
        }

        // Periodo de noches (excluye la fecha de salida)
        $period = CarbonPeriod::create($data['check_in'], $data['check_out'])->excludeEndDate();
        $dates  = collect($period)->map(fn($d) => $d->toDateString());
        if ($dates->isEmpty()) {
            return back()->withErrors(['check_in' => 'Rango de fechas inválido.'])->withInput();
        }

        // Solape con reservas existentes (no canceladas)
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

        // Calendario de tarifas
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

        // Estancia mínima
        $minStay = $rates->pluck('min_stay')->filter()->min() ?? 2;
        if ($dates->count() < $minStay) {
            return back()->withErrors(['check_in' => "La estancia mínima es de $minStay noches."])->withInput();
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

        return redirect()->route('customer.bookings')->with('status', 'Reserva creada. Total: '.$total.' €');
    }

    /**
     * Listado de reservas del cliente autenticado.
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
