<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\PaymentReceiptMail;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Simula el pago de una reserva y genera una factura.
     */
    public function pay(int $reservationId)
    {
        $reservation = Reservation::with(['user', 'property'])
            ->where('id', $reservationId)
            ->where('status', 'pending')
            ->firstOrFail();

        // Guardamos la factura que se crea
        $invoice = DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'paid']);

            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'        => $reservation->total_price,
                'method'        => 'simulated',
                'status'        => 'succeeded',
                'provider_ref'  => 'SIM-' . Str::upper(Str::random(8)),
            ]);

            $count = Invoice::count() + 1;
            $invoiceNumber = 'INV-' . now()->year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

            return Invoice::create([
                'reservation_id' => $reservation->id,
                'number'         => $invoiceNumber,
                'pdf_path'       => null,
                'issued_at'      => now(),
                'amount'         => $reservation->total_price,
            ]);
        });

        // Cargamos por si faltaran relaciones
        $reservation->loadMissing(['user', 'property']);

        // Enviar email con vista (no romper si falla)
        try {
            \Mail::to($reservation->user->email)->send(
                new PaymentReceiptMail($reservation, $invoice)
            );
        } catch (\Throwable $e) {
            \Log::error('Fallo enviando PaymentReceiptMail', [
                'msg'  => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }

        return back()->with('success', 'Pago simulado realizado y factura generada.');
    }
}
