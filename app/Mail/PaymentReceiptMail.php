<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;
use App\Models\Invoice;

class PaymentReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Crea una nueva instancia de mensaje.
     * @param \App\Models\Reservation $reservation recibe la reserva
     * @param \App\Models\Invoice $invoice recibe la factura
     */
    
    public function __construct(
        public Reservation $reservation,
        public Invoice $invoice
    ) {}

    /**
     * Define el sobre del mensaje (asunto, etc.)
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pago confirmado · ' . $this->invoice->number,
        );
    }

    /**
     * Define el contenido del mensaje (vista y datos)
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_receipt',
            with: [
                'reservation' => $this->reservation->loadMissing(['user','property']),
                'invoice'     => $this->invoice,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
