<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminPaymentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public Invoice $invoice
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pago recibido · ' . $this->invoice->number
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_payment_notification',
            with: [
                'reservation' => $this->reservation->loadMissing(['user','property']),
                'invoice'     => $this->invoice,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
