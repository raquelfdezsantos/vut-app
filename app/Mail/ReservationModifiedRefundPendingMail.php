<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationModifiedRefundPendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $reservation,
        public $newTotal,
        public $refundAmount
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reserva #' . $this->reservation->id . ' modificada - Devolución pendiente',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_modified_refund_pending',
            with: [
                'reservation' => $this->reservation->loadMissing(['user', 'property']),
                'newTotal' => $this->newTotal,
                'refundAmount' => $this->refundAmount,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
