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
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Support\Str;
use App\Models\Property;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationCancelledMail;
use App\Mail\PaymentRefundIssuedMail;
use App\Mail\ReservationUpdatedMail;
use App\Mail\PropertyDeletedConfirmationMail;

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

        // Estadísticas para el dashboard
        $stats = [
            // Reservas activas (pending + paid)
            'activeReservations' => Reservation::whereIn('status', ['pending', 'paid'])->count(),
            
            // Ingresos totales (solo reservas pagadas)
            'totalRevenue' => Reservation::where('status', 'paid')->sum('total_price'),
            
            // Ocupación del mes actual (%)
            'occupancyRate' => $this->calculateOccupancyRate(),
            
            // Próximas 5 reservas
            'upcomingReservations' => Reservation::with(['user', 'property'])
                ->whereIn('status', ['pending', 'paid'])
                ->where('check_in', '>=', now())
                ->orderBy('check_in')
                ->limit(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('reservations', 'stats'));
    }

    /**
     * Calcula el porcentaje de ocupación del mes actual.
     */
    private function calculateOccupancyRate(): float
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        // Contar noches reservadas en el mes (status = paid o pending)
        $bookedNights = Reservation::whereIn('status', ['pending', 'paid'])
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('check_in', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('check_out', [$startOfMonth, $endOfMonth])
                  ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                      $q2->where('check_in', '<=', $startOfMonth)
                         ->where('check_out', '>=', $endOfMonth);
                  });
            })
            ->get()
            ->sum(function ($reservation) use ($startOfMonth, $endOfMonth) {
                $checkIn = $reservation->check_in->max($startOfMonth);
                $checkOut = $reservation->check_out->min($endOfMonth);
                return $checkIn->diffInDays($checkOut);
            });

        return $daysInMonth > 0 ? round(($bookedNights / $daysInMonth) * 100, 1) : 0;
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

    /**
     * Soft delete de la propiedad con cancelación de reservas futuras.
     */
    public function destroyProperty(Property $property)
    {
        DB::beginTransaction();

        try {
            // 1. Obtener reservas futuras activas (pending o paid)
            $futureReservations = Reservation::where('property_id', $property->id)
                ->where('check_in', '>=', now())
                ->whereIn('status', ['pending', 'paid'])
                ->get();

            $cancelledCount = 0;
            $totalRefunded = 0.0;

            // 2. Cancelar cada reserva futura
            foreach ($futureReservations as $reservation) {
                // Liberar fechas del calendario
                $period = CarbonPeriod::create($reservation->check_in, $reservation->check_out->subDay());
                foreach ($period as $date) {
                    RateCalendar::where('property_id', $property->id)
                        ->where('date', $date->toDateString())
                        ->update(['is_available' => true, 'blocked_by' => null]);
                }

                // Si estaba pagada, registrar reembolso
                if ($reservation->status === 'paid') {
                    $refund = Payment::create([
                        'reservation_id' => $reservation->id,
                        'amount' => -$reservation->total_price,
                        'method' => 'refund',
                        'status' => 'refunded',
                        'provider_ref' => 'refund_' . Str::uuid(),
                    ]);

                    $totalRefunded += $reservation->total_price;

                    // Enviar email de reembolso al cliente
                    if ($reservation->user && $reservation->user->email) {
                        Mail::to($reservation->user->email)->send(
                            new PaymentRefundIssuedMail($reservation, $refund)
                        );
                    }
                }

                // Marcar reserva como cancelada
                $reservation->update(['status' => 'cancelled']);

                // Enviar email de cancelación al cliente
                if ($reservation->user && $reservation->user->email) {
                    Mail::to($reservation->user->email)->send(
                        new ReservationCancelledMail($reservation)
                    );
                }

                $cancelledCount++;
            }

            // 3. Soft delete de la propiedad
            $property->delete();

            // 4. Enviar email de confirmación al admin
            $admin = Auth::user();
            if ($admin && $admin->email) {
                Mail::to($admin->email)->send(
                    new PropertyDeletedConfirmationMail(
                        $property->name ?? 'Propiedad',
                        $cancelledCount,
                        $totalRefunded
                    )
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.dashboard')
                ->with('success', 
                    "Propiedad dada de baja. Canceladas {$cancelledCount} reserva(s). Reembolsado: " . 
                    number_format($totalRefunded, 2, ',', '.') . " €"
                );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al dar de baja la propiedad: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario de edición de la propiedad.
     */
    public function propertyEdit()
    {
        $property = Property::withTrashed()->first();
        
        if (!$property) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No hay ninguna propiedad en el sistema.');
        }

        // Contar reservas futuras activas
        $futureReservationsCount = Reservation::where('property_id', $property->id)
            ->where('check_in', '>=', now())
            ->whereIn('status', ['pending', 'paid'])
            ->count();

        return view('admin.property.index', compact('property', 'futureReservationsCount'));
    }

    /**
     * Actualiza los datos de la propiedad.
     */
    public function propertyUpdate(Request $request, Property $property)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:100',
            'capacity' => 'required|integer|min:1|max:50',
        ]);

        $property->update($validated);

        return back()->with('success', 'Propiedad actualizada correctamente.');
    }

    /**
     * Muestra la página de gestión de fotos.
     */
    public function photosIndex()
    {
        $property = Property::withTrashed()->first();
        
        if (!$property) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No hay ninguna propiedad en el sistema.');
        }

        // Cargar fotos ordenadas por sort_order
        $photos = $property->photos()->orderBy('sort_order')->get();

        return view('admin.photos.index', compact('property', 'photos'));
    }

    /**
     * Sube una o varias fotos a la propiedad.
     */
    public function photosStore(Request $request)
    {
        $property = Property::first();
        
        if (!$property) {
            return back()->with('error', 'No hay ninguna propiedad en el sistema.');
        }

        $request->validate([
            'photos' => 'required|array|min:1|max:10',
            'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB máximo
        ]);

        // Obtener el último sort_order
        $lastSortOrder = $property->photos()->max('sort_order') ?? 0;

        foreach ($request->file('photos') as $index => $photo) {
            // Guardar archivo en storage/app/public/photos
            $path = $photo->store('photos', 'public');

            // Crear registro en BD
            $property->photos()->create([
                'url' => $path,
                'sort_order' => $lastSortOrder + $index + 1,
                'is_cover' => false, // Por defecto no es portada
            ]);
        }

        $count = count($request->file('photos'));
        return back()->with('success', "{$count} foto(s) subida(s) correctamente.");
    }

    /**
     * Elimina una foto.
     */
    public function photosDestroy($photoId)
    {
        $photo = \App\Models\Photo::findOrFail($photoId);

        // Eliminar archivo físico solo si es local (no URL externa)
        if (!str_starts_with($photo->url, 'http')) {
            \Storage::disk('public')->delete($photo->url);
        }

        // Eliminar registro de BD
        $photo->delete();

        return back()->with('success', 'Foto eliminada correctamente.');
    }

    /**
     * Actualiza el orden de las fotos.
     */
    public function photosReorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:photos,id',
        ]);

        foreach ($request->order as $index => $photoId) {
            \App\Models\Photo::where('id', $photoId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Marca una foto como portada (is_cover).
     */
    public function photosSetCover($photoId)
    {
        $photo = \App\Models\Photo::findOrFail($photoId);
        
        // Quitar is_cover de todas las fotos de la propiedad
        \App\Models\Photo::where('property_id', $photo->property_id)->update(['is_cover' => false]);
        
        // Marcar la seleccionada
        $photo->update(['is_cover' => true]);

        return back()->with('success', 'Foto marcada como portada.');
    }
}
