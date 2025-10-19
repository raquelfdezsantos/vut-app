<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Mail\PaymentReceiptMail;
use App\Mail\PaymentBalanceSettledMail;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminPaymentNotificationMail;
use App\Mail\AdminPaymentBalanceSettledMail;
use App\Mail\ReservationUpdatedMail;
use App\Mail\ReservationCancelledMail;
use App\Mail\PaymentRefundIssuedMail;
use App\Mail\PaymentBalanceDueMail;

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

        // Enviar emails (cliente y admin), no romper si falla
        try {
            Mail::to($reservation->user->email)->send(
                new PaymentReceiptMail($reservation, $invoice)
            );
        } catch (\Throwable $e) {
            Log::error('Fallo enviando PaymentReceiptMail', ['msg' => $e->getMessage()]);
        }

        try {
            Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(
                new AdminPaymentNotificationMail($reservation, $invoice)
            );
        } catch (\Throwable $e) {
            Log::error('Fallo enviando AdminPaymentNotificationMail', ['msg' => $e->getMessage()]);
        }

        return back()->with('success', 'Pago simulado realizado y factura generada.');
    }


    public function payDifference(int $reservationId)
    {
        $reservation = Reservation::with(['user', 'property', 'payments'])->findOrFail($reservationId);
    abort_unless($reservation->user_id === Auth::id() || (Auth::user() && Auth::user()->role === 'admin'), 403);

        $balance = $reservation->balanceDue();
        if ($balance <= 0.0) {
            return back()->with('status', 'No hay importe pendiente.');
        }

        DB::transaction(function () use ($reservation, $balance) {
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'        => $balance,
                'method'        => 'simulated',
                'status'        => 'succeeded',
                'provider_ref'  => 'SIM-ADD-' . Str::upper(Str::random(6)),
            ]);
        });

        // Emails (cliente y admin), no romper si falla
        try {
            Mail::to($reservation->user->email)->send(
                new PaymentBalanceSettledMail($reservation, $balance)
            );
        } catch (\Throwable $e) {
            Log::error('Fallo enviando PaymentBalanceSettledMail', ['msg' => $e->getMessage()]);
        }
        try {
            Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(
                new AdminPaymentBalanceSettledMail($reservation, $balance)
            );
        } catch (\Throwable $e) {
            Log::error('Fallo enviando AdminPaymentBalanceSettledMail', ['msg' => $e->getMessage()]);
        }

        return back()->with('success', 'Diferencia abonada correctamente.');
    }
}
