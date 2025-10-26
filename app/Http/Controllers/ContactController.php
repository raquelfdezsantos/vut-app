<?php 

namespace App\Http\Controllers;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller {
    public function store(ContactRequest $request) {
        // si honeypot viene relleno, ignorar
        if($request->filled('website')) return back()->with('success','Mensaje enviado.');
        Mail::to(config('mail.admin_to', env('MAIL_ADMIN','admin@vut.test')))
            ->queue(new ContactMessageMail($request->validated()));
        return back()->with('success','¡Gracias! Te responderemos pronto.');
    }
}