<?php

namespace App\Mail;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropertyDeletedConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $propertyName;
    public $cancelledReservations;
    public $totalRefunded;

    /**
     * Create a new message instance.
     */
    public function __construct(string $propertyName, int $cancelledReservations, float $totalRefunded)
    {
        $this->propertyName = $propertyName;
        $this->cancelledReservations = $cancelledReservations;
        $this->totalRefunded = $totalRefunded;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación: Propiedad dada de baja',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.property-deleted-confirmation',
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
