<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use App\Mail\PaymentReceiptMail;
use App\Mail\AdminPaymentNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Throwable;

class StripeController extends Controller
{
    public function checkout(Request $request, Reservation $reservation)
    {
        $this->authorize('pay', $reservation);

        abort_unless($reservation->status === 'pending', 403);

        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('stripe.cancel'),
            'customer_email' => $reservation->user->email,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int) round($reservation->total_price * 100),
                    'product_data' => [
                        'name' => 'Reserva ' . $reservation->property->name,
                        'description' => $reservation->check_in->format('d/m/Y') . ' → ' . $reservation->check_out->format('d/m/Y'),
                    ],
                ],
            ]],
            'metadata' => [
                'reservation_id' => (string) $reservation->id,
            ],
        ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        // 1) Recuperar session_id que Stripe añade al return_url
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('reservas.index')->with('error', 'Pago inválido.');
        }

        // Configurar clave secreta de Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        // 2) Consultar a Stripe (SDK ya configurado con STRIPE_SECRET_KEY en el constructor)
        $session = Session::retrieve($sessionId);

        // 3) Validar estado
        if (($session->payment_status ?? '') !== 'paid') {
            return redirect()->route('reservas.index')->with('error', 'Pago no completado.');
        }

        // 4) Sacar la reserva desde metadata
        $reservationId = $session->metadata->reservation_id ?? null;
        if (!$reservationId) {
            return redirect()->route('reservas.index')->with('error', 'Reserva no encontrada en el pago.');
        }

        $reservation = Reservation::with(['user', 'property', 'payments'])->findOrFail($reservationId);

        $this->authorize('pay', $reservation);
        
        // Idempotencia: si ya está paid y existe un pago “succeeded”, no repetir
        if ($reservation->status === 'paid' && $reservation->payments()->where('status', 'succeeded')->exists()) {
            return redirect()->route('reservas.index')->with('success', 'Pago confirmado.');
        }

        // 5) Escribir en BD: Payment + Invoice + marcar reserva paid
        $invoice = DB::transaction(function () use ($reservation, $session) {
            // crear payment
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'        => $reservation->total_price,
                'method'        => 'stripe',
                'status'        => 'succeeded',
                'provider_ref'  => $session->payment_intent ?? ('CS_' . $session->id),
            ]);

            // marcar paid
            $reservation->update(['status' => 'paid']);

            // generar nº de factura
            $count = Invoice::count() + 1;
            $number = 'INV-' . now()->year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

            return Invoice::create([
                'reservation_id' => $reservation->id,
                'number'         => $number,
                'pdf_path'       => null,
                'issued_at'      => now(),
                'amount'         => $reservation->total_price,
            ]);
        });

        // 6) Enviar correos (cliente + admin). Reutiliza tus mailables
        try {
            Mail::to($reservation->user->email)->queue(
                new PaymentReceiptMail($reservation, $invoice)
            );
        } catch (Throwable $e) {
            Log::error('Fallo enviando PaymentReceiptMail (Stripe success): ' . $e->getMessage());
        }

        try {
            Mail::to(config('mail.admin_to', env('MAIL_ADMIN', 'admin@vut.test')))->queue(
                new AdminPaymentNotificationMail($reservation, $invoice)
            );
        } catch (Throwable $e) {
            Log::error('Fallo enviando AdminPaymentNotificationMail (Stripe success): ' . $e->getMessage());
        }

        return redirect()->route('reservas.index')->with('success', 'Pago realizado correctamente.');
    }


    public function cancel()
    {
        return redirect()->route('reservas.index')->with('error', 'Pago cancelado por el usuario.');
    }
}
