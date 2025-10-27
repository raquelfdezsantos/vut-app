<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Mail::to(config('mail.admin_to', env('MAIL_ADMIN', 'admin@vut.test')))
            ->send(new ContactMessageMail($data));

        return back()->with('success', '¡Gracias! Te responderemos pronto.');
    }
}
