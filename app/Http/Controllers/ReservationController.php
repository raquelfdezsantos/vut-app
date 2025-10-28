<?php

namespace App\Http\Controllers;

use App\Models\Payment;
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
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\ReservationUpdatedMail;
use App\Mail\ReservationCancelledMail;
use App\Mail\PaymentRefundIssuedMail;
use App\Mail\PaymentBalanceDueMail;
use Throwable;
use Illuminate\Support\Facades\Log;


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
            ->where('user_id', \Auth::id())
            ->latest('check_in')
            ->paginate(10);

        $suggested = Property::select('id', 'slug', 'name')->first();

        return view('customer.bookings.index', compact('reservations', 'suggested'));
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

        $total = $rates->sum('price') * (int)$data['guests'];

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
                    ->update(['is_available' => false, 'blocked_by' => 'reservation']);
            }

            return $reservation;
        });


        // Emails (no romper si falla SMTP)
        try {
            Mail::to($reservation->user->email)->send(new ReservationConfirmedMail($reservation));
        } catch (Throwable $e) {
            report($e);
        }

        try {
            Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))
                ->send(new AdminNewReservationMail($reservation));
        } catch (Throwable $e) {
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


    private function rangeDates(string $from, string $to): array
    {
        // [from, to) excluye la salida
        $period = CarbonPeriod::create($from, $to)->excludeEndDate();
        return collect($period)->map(fn($d) => $d->toDateString())->all();
    }

    private function setAvailability(int $propertyId, array $dates, bool $available): void
    {
        if (empty($dates)) return;
        RateCalendar::where('property_id', $propertyId)
            ->whereIn('date', $dates)
            ->update(['is_available' => $available]);
    }

    /** Form de edición (cliente, permite pending y paid) */
    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        if ($reservation->status === 'cancelled') {
            return back()->with('error', 'No puedes modificar reservas canceladas.');
        }
        $reservation->loadMissing('property');
        return view('customer.bookings.edit', compact('reservation'));
    }

    /** Update fechas (cliente, permite pending y paid) */
    public function update(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        if ($reservation->status === 'cancelled') {
            return back()->with('error', 'No puedes modificar reservas canceladas.');
        }

        $data = $request->validate([
            'check_in'  => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests'    => ['required', 'integer', 'min:1'],
        ]);

        $property = $reservation->property()->firstOrFail();

        if ((int)$data['guests'] > (int)$property->capacity) {
            return back()->withErrors(['guests' => "Máximo {$property->capacity} huéspedes."]);
        }

        $oldDates = $this->rangeDates($reservation->check_in->toDateString(), $reservation->check_out->toDateString());
        $newDates = $this->rangeDates($data['check_in'], $data['check_out']);

        if (empty($newDates)) {
            return back()->withErrors(['check_in' => 'La fecha de salida debe ser posterior a la de entrada.']);
        }

        // Validar solapes en [check_in, check_out)
        $overlap = Reservation::where('property_id', $property->id)
            ->where('id', '!=', $reservation->id)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($data) {
                $q->where('check_in', '<', $data['check_out'])
                    ->where('check_out', '>', $data['check_in']);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['check_in' => 'Las nuevas fechas se solapan con otra reserva.']);
        }

        // Comprobar disponibilidad de noches nuevas
        $rates = RateCalendar::where('property_id', $property->id)
            ->whereIn('date', $newDates)
            ->get()
            ->keyBy('date');

        foreach ($newDates as $d) {
            $rate = $rates->get($d);
            // Si el día nuevo no existe o no está libre y no pertenece al rango antiguo, error
            if (!$rate || (!$rate->is_available && !in_array($d, $oldDates, true))) {
                return back()->withErrors(['check_in' => "No hay disponibilidad el día $d."]);
            }
        }

        // Reglas de min_stay (opcional)
        $nights = count($newDates);
        $minStay = $rates->pluck('min_stay')->filter()->min();
        if ($minStay && $nights < $minStay) {
            return back()->withErrors(['check_in' => "La estancia mínima para esas fechas es de {$minStay} noches."]);
        }

        $newTotal = $rates->sum('price') * (int)$data['guests'];

        DB::transaction(function () use ($reservation, $property, $oldDates, $newDates, $newTotal, $data) {
            // liberar antiguas y bloquear nuevas (excluye checkout)
            $this->setAvailability($property->id, $oldDates, true);
            $this->setAvailability($property->id, $newDates, false);

            $reservation->update([
                'check_in'    => $data['check_in'],
                'check_out'   => $data['check_out'],
                'guests'      => $data['guests'],
                'total_price' => $newTotal,
            ]);
        });

        $paid   = $reservation->paidAmount(); // helper del modelo
        $diff   = $reservation->total_price - $paid; // >0 falta cobrar, <0 hay que devolver

        // Emails con Mailables (cliente y admin)
        Log::info('Intentando enviar ReservationUpdatedMail al cliente', ['email' => $reservation->user->email]);
        try {
            Mail::to($reservation->user->email)->send(new ReservationUpdatedMail($reservation));
            Log::info('ReservationUpdatedMail enviado al cliente', ['email' => $reservation->user->email]);
        } catch (Throwable $e) {
            Log::error('Fallo ReservationUpdatedMail cliente', ['msg' => $e->getMessage()]);
            report($e);
        }
        Log::info('Intentando enviar ReservationUpdatedMail al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test')]);
        try {
            Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(new ReservationUpdatedMail($reservation));
            Log::info('ReservationUpdatedMail enviado al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test')]);
        } catch (Throwable $e) {
            Log::error('Fallo ReservationUpdatedMail admin', ['msg' => $e->getMessage()]);
            report($e);
        }

        // Si falta cobrar (diff > 0) - no tocamos estado; botón “Pagar diferencia” se mostrará en la vista
        // Si sobra dinero (diff < 0) - refund simulado
        if ($diff < 0) {
            $refund = abs($diff);
            DB::transaction(function () use ($reservation, $refund) {
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount'        => -$refund, // negativo = devolución
                    'method'        => 'simulated',
                    'status'        => 'refunded',
                    'provider_ref'  => 'SIM-REF-' . Str::upper(Str::random(6)),
                ]);
            });

            try {
                Mail::to($reservation->user->email)->send(new PaymentRefundIssuedMail($reservation, $refund));
            } catch (Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('reservas.index')->with('success', 'Reserva actualizada correctamente.');
    }

    /** Cancelar (cliente, permite pending y paid) */
    public function cancel(Reservation $reservation)
    {
        $this->authorize('cancel', $reservation);
        if ($reservation->status === 'cancelled') {
            return back()->with('error', 'La reserva ya está cancelada.');
        }

        DB::transaction(function () use ($reservation) {
            $dates = $this->rangeDates($reservation->check_in->toDateString(), $reservation->check_out->toDateString());
            $this->setAvailability($reservation->property_id, $dates, true);
            $reservation->update(['status' => 'cancelled']);
        });

        // Emails de cancelación (cliente y admin)
        Log::info('Intentando enviar ReservationCancelledMail al cliente', ['email' => $reservation->user->email]);
        try {
            Mail::to($reservation->user->email)->send(new ReservationCancelledMail($reservation));
            Log::info('ReservationCancelledMail enviado al cliente', ['email' => $reservation->user->email]);
        } catch (Throwable $e) {
            Log::error('Fallo ReservationCancelledMail cliente', ['msg' => $e->getMessage()]);
            report($e);
        }
        Log::info('Intentando enviar ReservationCancelledMail al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test')]);
        try {
            Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(new ReservationCancelledMail($reservation));
            Log::info('ReservationCancelledMail enviado al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test')]);
        } catch (Throwable $e) {
            Log::error('Fallo ReservationCancelledMail admin', ['msg' => $e->getMessage()]);
            report($e);
        }

        return back()->with('success', 'Reserva cancelada y noches liberadas.');
    }
}
