<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class ReservationConfirmedMail extends Mailable
{
    use Queueable, SerializesModels; 

    /**
     * Crea una nueva instancia de mensaje.
     * @param \App\Models\Reservation $reservation recibe la reserva confirmada
     */
    public function __construct(public Reservation $reservation)
    {
        //
    }

    /**
     * Confirma la reserva al usuario
     * @return ReservationConfirmedMail devuelve el mensaje construido
     */
    public function build()
    {
        return $this->subject('Reserva creada correctamente')
            ->view('emails.reservation_confirmed');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reservation Confirmed Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
