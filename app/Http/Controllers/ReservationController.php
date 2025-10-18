<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\RateCalendar;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationConfirmedMail;
use App\Mail\AdminNewReservationMail;
use Carbon\Carbon;

/**
 * Controlador de reservas.
 *
 * Gestiona la creación, validación y visualización de reservas tanto
 * para clientes como para el administrador. Controla las reglas de negocio
 * sobre disponibilidad, estancia mínima, solapamientos y capacidad máxima.
 */
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


    /** Listado de reservas del cliente */
    public function index()
    {
        $reservations = Reservation::with(['property', 'invoice'])
            ->where('user_id', Auth::id())
            ->latest('check_in')
            ->paginate(10);

        // Usa la vista que hay en carpeta:
        return view('customer.bookings.index', compact('reservations'));
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

        // --- Fallback: crear filas que falten en RateCalendar para el rango ---
        $missingDates = $dates->filter(function ($d) use ($property) {
            return !RateCalendar::where('property_id', $property->id)
                ->whereDate('date', $d)
                ->exists();
        });

        foreach ($missingDates as $d) {
            $dateObj = Carbon::parse($d);
            $price = $dateObj->isWeekend() ? 120.00 : 95.00;
            RateCalendar::create([
                'property_id'  => $property->id,
                'date'         => $d,
                'price'        => $price,
                'is_available' => true,
                'min_stay'     => 2,
            ]);
        }
        // ---------------------------------------------------------------------

        $overlap = Reservation::where('property_id', $property->id)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($data) {
                $q->where('check_in',  '<', $data['check_out'])
                    ->where('check_out', '>', $data['check_in']);
            })
            ->exists();

        if ($overlap) {
            return back()
                ->withErrors(['check_in' => 'Las fechas seleccionadas no están disponibles.'])
                ->withInput();
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

        $total = $rates->sum('price');

        // Transacción: crear reserva + marcar noches como NO disponibles
        $reservation = DB::transaction(function () use ($data, $property, $total) {
            $reservation = Reservation::create([
                'user_id'     => Auth::id(),
                'property_id' => $property->id,
                'check_in'    => $data['check_in'],
                'check_out'   => $data['check_out'],
                'guests'      => $data['guests'],
                'status'      => 'pending',
                'total_price' => $total,
            ]);

            // Bloquear noches [check_in, check_out)
            $period = CarbonPeriod::create($data['check_in'], $data['check_out'])->excludeEndDate();
            foreach ($period as $d) {
                RateCalendar::where('property_id', $property->id)
                    ->whereDate('date', $d->toDateString())
                    ->update(['is_available' => false]);
            }

            return $reservation;
        });


        // Emails (no romper si falla SMTP)
        try {
            Mail::to($reservation->user->email)->send(new ReservationConfirmedMail($reservation));
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            Mail::to('admin@vut.test')->send(new AdminNewReservationMail($reservation));
        } catch (\Throwable $e) {
            report($e);
        }

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
