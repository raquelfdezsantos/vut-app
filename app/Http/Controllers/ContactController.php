<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Property;

class ContactController extends Controller
{
    public function create()
    {
        // Pasar la primera propiedad disponible para mostrar dirección y coordenadas
        $property = Property::first();
        return view('contact.form', compact('property'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Por favor, introduce un email válido.',
            'email.max' => 'El email no puede superar los 150 caracteres.',
            'subject.required' => 'El asunto es obligatorio.',
            'subject.max' => 'El asunto no puede superar los 150 caracteres.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.max' => 'El mensaje no puede superar los 2000 caracteres.',
        ]);

        Mail::to(config('mail.admin_to', env('MAIL_ADMIN', 'admin@vut.test')))
            ->send(new ContactMessageMail($data));

        return back()->with('success', '¡Gracias! Te responderemos pronto.');
    }
}
