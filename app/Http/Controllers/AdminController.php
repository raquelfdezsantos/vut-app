<?php

/**
 * -----------------------------------------------------------------------------
 *  Descripción:
 *    Controlador del área de administración. Permite:
 *      - Ver el listado de reservas con filtros básicos.
 *      - Cancelar reservas pendientes y reponer noches en el calendario.
 *  Notas:
 *    - Requiere middleware de autenticación y rol 'admin'.
 *    - La reposición de noches afecta a RateCalendar indicando disponibilidad=true.
 * -----------------------------------------------------------------------------
 */

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\RateCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Support\Str;
use App\Models\Property;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationCancelledMail;
use App\Mail\PaymentRefundIssuedMail;
use App\Mail\ReservationUpdatedMail;

class AdminController extends Controller
{
    /**
     * Muestra el dashboard del administrador con el listado de reservas.
     *
     * Filtros opcionales por estado (?status=pending|paid|cancelled) y por rango
     * de fechas (?from=YYYY-MM-DD&to=YYYY-MM-DD).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'property', 'invoice'])->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($from = $request->date('from')) {
            $query->whereDate('check_in', '>=', $from);
        }
        if ($to = $request->date('to')) {
            $query->whereDate('check_out', '<=', $to);
        }

        $reservations = $query->paginate(10)->withQueryString();

        return view('admin.dashboard', compact('reservations'));
    }

    /**
     * Cancela una reserva "pending" y repone las noches al calendario.
     *
     * Reglas:
     *  - Solo reservas con estado 'pending' pueden cancelarse aquí.
     *  - Reposición: marca como disponibles (is_available=true) las fechas del
     *    rango [check_in, check_out) para la propiedad asociada.
     *
     * @param  int  $reservationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(int $reservationId)
    {
        $reservation = Reservation::query()
            ->where('id', $reservationId)
            ->with('property')
            ->firstOrFail();

        // Solo cancelamos si está pendiente
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Solo es posible cancelar reservas pendientes.');
        }

        DB::transaction(function () use ($reservation) {
            // 1) Actualiza estado de la reserva
            $reservation->update(['status' => 'cancelled']);

            // 2) Restaura disponibilidad en RateCalendar
            $start = $reservation->check_in->copy();
            $end   = $reservation->check_out->copy();

            // Reponemos cada día del rango [check_in, check_out)
            for ($date = $start->copy(); $date->lt($end); $date->addDay()) {
                RateCalendar::where('property_id', $reservation->property_id)
                    ->whereDate('date', $date->toDateString())
                    ->update(['is_available' => true, 'blocked_by' => null]);
            }
        });

        // Notificaciones de cancelación (cliente y admin)
        \Log::info('Intentando enviar ReservationCancelledMail al cliente', ['email' => $reservation->user->email]);
        try {
            \Mail::to($reservation->user->email)->send(new ReservationCancelledMail($reservation));
            \Log::info('ReservationCancelledMail enviado al cliente', ['email' => $reservation->user->email]);
        } catch (\Throwable $e) {
            \Log::error('Fallo ReservationCancelledMail cliente', ['msg' => $e->getMessage()]);
            report($e);
        }
        \Log::info('Intentando enviar ReservationCancelledMail al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test')]);
        try {
            \Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(new ReservationCancelledMail($reservation));
            \Log::info('ReservationCancelledMail enviado al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test')]);
        } catch (\Throwable $e) {
            \Log::error('Fallo ReservationCancelledMail admin', ['msg' => $e->getMessage()]);
            report($e);
        }

        return back()->with('success', 'Reserva cancelada, noches repuestas y notificada.');
    }


    private function rangeDates(string $from, string $to): array
    {
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

    /** Form edición (admin) */
    public function edit(int $id)
    {
        $reservation = Reservation::with('property', 'user')->findOrFail($id);
        return view('admin.reservations.edit', compact('reservation')); // crea vista simple
    }

    /** Update (admin) — permite pending/paid */
    public function update(Request $request, int $id)
    {
        $reservation = Reservation::with('property')->findOrFail($id);

        $data = $request->validate([
            'check_in'  => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests'    => ['required', 'integer', 'min:1'],
        ]);

        $property = $reservation->property;
        if ((int)$data['guests'] > (int)$property->capacity) {
            return back()->withErrors(['guests' => "Máximo {$property->capacity} huéspedes."]);
        }

        $oldDates = $this->rangeDates($reservation->check_in->toDateString(), $reservation->check_out->toDateString());
        $newDates = $this->rangeDates($data['check_in'], $data['check_out']);

        // Solapes con otras reservas
        $overlap = Reservation::where('property_id', $property->id)
            ->where('id', '!=', $reservation->id)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($data) {
                $q->where('check_in', '<', $data['check_out'])
                    ->where('check_out', '>', $data['check_in']);
            })
            ->exists();
        if ($overlap) {
            return back()->withErrors(['check_in' => 'Solapa con otra reserva.']);
        }

        $rates = RateCalendar::where('property_id', $property->id)
            ->whereIn('date', $newDates)->get()->keyBy('date');

        foreach ($newDates as $d) {
            $rate = $rates->get($d);
            if (!$rate || (!$rate->is_available && !in_array($d, $oldDates, true))) {
                return back()->withErrors(['check_in' => "No hay disponibilidad el día $d."]);
            }
        }

        $newTotal = $rates->sum('price') * (int)$data['guests'];

        DB::transaction(function () use ($reservation, $property, $oldDates, $newDates, $newTotal, $data) {
            $this->setAvailability($property->id, $oldDates, true);
            $this->setAvailability($property->id, $newDates, false);

            $reservation->update([
                'check_in'    => $data['check_in'],
                'check_out'   => $data['check_out'],
                'guests'      => $data['guests'],
                'total_price' => $newTotal,
            ]);
        });

        // Notificaciones por email (cliente y admin)
        \Log::info('Intentando enviar ReservationUpdatedMail al cliente', ['email' => $reservation->user->email]);
        try {
            \Mail::to($reservation->user->email)->send(new ReservationUpdatedMail($reservation));
            \Log::info('ReservationUpdatedMail enviado al cliente', ['email' => $reservation->user->email]);
        } catch (\Throwable $e) {
            \Log::error('Fallo ReservationUpdatedMail cliente', ['msg' => $e->getMessage()]);
            report($e);
        }
        \Log::info('Intentando enviar ReservationUpdatedMail al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test')]);
        try {
            \Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(new ReservationUpdatedMail($reservation));
            \Log::info('ReservationUpdatedMail enviado al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test')]);
        } catch (\Throwable $e) {
            \Log::error('Fallo ReservationUpdatedMail admin', ['msg' => $e->getMessage()]);
            report($e);
        }

        // Si hay diferencia a devolver, simular refund y notificar
        $paid = method_exists($reservation, 'paidAmount') ? $reservation->paidAmount() : 0;
        $diff = $reservation->total_price - $paid;
        if ($diff < 0) {
            $refund = abs($diff);
            DB::transaction(function () use ($reservation, $refund) {
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount'        => -$refund,
                    'method'        => 'simulated',
                    'status'        => 'refunded',
                    'provider_ref'  => 'SIM-REF-' . Str::upper(Str::random(6)),
                ]);
            });
            try {
                \Mail::to($reservation->user->email)->send(new PaymentRefundIssuedMail($reservation, $refund));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'Reserva actualizada y notificada.');
    }


    public function refund(int $id)
    {
        $reservation = Reservation::with('property')->findOrFail($id);

        if ($reservation->status !== 'paid') {
            return back()->with('error', 'Solo reservas pagadas pueden reembolsarse.');
        }

        $refund = $reservation->total_price;
        DB::transaction(function () use ($reservation, $refund) {
            // 1) Cancelar y reponer noches
            $reservation->update(['status' => 'cancelled']);

            for ($d = $reservation->check_in->copy(); $d->lt($reservation->check_out); $d->addDay()) {
                RateCalendar::where('property_id', $reservation->property_id)
                    ->whereDate('date', $d->toDateString())
                    ->update(['is_available' => true, 'blocked_by' => null]);
            }

            // 2) Registrar “reembolso” simulado
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'         => $refund, // total reembolsado
                'method'         => 'simulated',
                'status'         => 'refunded',
                'provider_ref'   => 'REF-' . Str::upper(Str::random(8)),
            ]);
        });

        // Notificaciones de cancelación y reembolso (cliente y admin)
        try {
            \Mail::to($reservation->user->email)->send(new ReservationCancelledMail($reservation));
        } catch (\Throwable $e) {
            report($e);
        }
        try {
            \Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(new ReservationCancelledMail($reservation));
        } catch (\Throwable $e) {
            report($e);
        }
        try {
            \Mail::to($reservation->user->email)->send(new PaymentRefundIssuedMail($reservation, $refund));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', 'Reserva cancelada, reembolso registrado y notificada.');
    }


    public function blockDates(Request $request)
    {
        $data = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'start'       => ['required', 'date'],
            'end'         => ['required', 'date', 'after_or_equal:start'], // end INCLUSIVO
        ]);

        $prop   = Property::findOrFail($data['property_id']);
        $start  = Carbon::parse($data['start'])->startOfDay();
        $end    = Carbon::parse($data['end'])->startOfDay(); // rango [start, end] INCLUSIVO

        // 1) No permitir bloquear si hay reservas (pending/paid) que solapen
        $overlap = Reservation::where('property_id', $prop->id)
            ->whereIn('status', ['pending', 'paid'])
            ->where('check_in', '<=', $end->copy()->addDay())   // Ajuste para rango inclusivo
            ->where('check_out', '>', $start)
            ->exists();

        if ($overlap) {
            return back()->with('error', 'No se puede bloquear: existen reservas que solapan el rango.');
        }

        // 2) Marcar is_available=false día a día (rango INCLUSIVO)
        DB::transaction(function () use ($prop, $start, $end) {
            for ($d = $start->clone(); $d->lte($end); $d->addDay()) { // lte = INCLUSIVO
                RateCalendar::updateOrCreate(
                    ['property_id' => $prop->id, 'date' => $d->toDateString()],
                    // Conserva price/min_stay si existe; si no, pon defaults
                    ['is_available' => false, 'blocked_by' => 'admin'] + (function () use ($prop, $d) {
                        $rc = RateCalendar::where('property_id', $prop->id)
                            ->where('date', $d->toDateString())->first();
                        return $rc ? [] : ['price' => 100, 'min_stay' => 2]; // defaults simples
                    })()
                );
            }
        });

        return back()->with('success', 'Noches bloqueadas correctamente.');
    }

    public function unblockDates(Request $request)
    {
        $data = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'start'       => ['required', 'date'],
            'end'         => ['required', 'date', 'after_or_equal:start'], // end INCLUSIVO
        ]);

        $prop  = Property::findOrFail($data['property_id']);
        $start = Carbon::parse($data['start'])->startOfDay();
        $end   = Carbon::parse($data['end'])->startOfDay();

        DB::transaction(function () use ($prop, $start, $end) {
            for ($d = $start->clone(); $d->lte($end); $d->addDay()) { // lte = INCLUSIVO
                RateCalendar::where('property_id', $prop->id)
                    ->where('date', $d->toDateString())
                    ->update(['is_available' => true, 'blocked_by' => null]);
                // si no existe fila, no hace nada (queda disponible por ausencia)
            }
        }); 

        return back()->with('success', 'Noches desbloqueadas correctamente.');
    }
}
