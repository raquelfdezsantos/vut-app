<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Payment;
use App\Models\RateCalendar;
use Carbon\CarbonPeriod;

class PaymentController extends Controller
{
    /**
     * Simula el pago de una reserva y genera una factura.
     *
     * @param int $reservationId ID de la reserva a pagar.
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito o error.
     */
    public function pay(int $reservationId)
    {
        $reservation = Reservation::where('id', $reservationId)
            ->where('status', 'pending')
            ->firstOrFail();

        DB::transaction(function () use ($reservation) {
            // 1) Marcar pagada la reserva
            $reservation->update(['status' => 'paid']);

            // 2) Registrar pago simulado
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'        => $reservation->total_price,
                'method'        => 'simulated',
                'status'        => 'succeeded',
                'provider_ref'  => 'SIM-' . Str::upper(Str::random(8)),
            ]);

            // 3) Número de factura (simple para demo)
            $count = Invoice::count() + 1;
            $invoiceNumber = 'INV-' . now()->year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

            // 4) Crear factura (pdf_path por ahora null)
            Invoice::create([
                'reservation_id' => $reservation->id,
                'number'         => $invoiceNumber,
                'pdf_path'       => null, // En un caso real, se generaría el PDF y se guardaría la ruta
                'issued_at'      => now(),
                'amount'         => $reservation->total_price,
            ]);

            // Si el total está vacío, recalcular desde el calendario
            if (empty($reservation->total_price) || $reservation->total_price <= 0) {
                $period = CarbonPeriod::create($reservation->check_in, $reservation->check_out)->excludeEndDate();
                $dates  = collect($period)->map(fn($d) => $d->toDateString());

                $rates = RateCalendar::where('property_id', $reservation->property_id)
                    ->whereIn('date', $dates->all())
                    ->get()
                    ->keyBy('date');

                $reservation->total_price = $rates->sum('price');
                $reservation->save();
            }
        });

        return back()->with('success', 'Pago simulado realizado y factura generada.');
    }
}
