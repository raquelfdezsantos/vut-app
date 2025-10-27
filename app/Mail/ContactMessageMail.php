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
        return $this->subject('Nueva consulta desde la web')
            ->view('emails.contact')
            ->with(['data' => $this->data]);
    }
}