<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminPaymentRefundIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $reservation,
        public $refundAmount
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Devolución completada (Admin) · Reserva #' . $this->reservation->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_payment_refund_issued',
            with: [
                'reservation' => $this->reservation->loadMissing(['user', 'property']),
                'refundAmount' => $this->refundAmount,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
