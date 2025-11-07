<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable implements ShouldQueue {
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data){ $this->data = $data; }

    public function build(){
        $subject = $this->data['subject'] ?? 'Nueva consulta desde la web';
        return $this->subject($subject)
            ->view('emails.contact')
            ->with(['data' => $this->data]);
    }
}