<?php 

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest {
    public function rules(): array {
        return [
            'name' => ['required','string','max:120'],
            'email' => ['required','email'],
            'message' => ['required','string','max:2000'],
            // anti-spam sencillo (honeypot)
            'website' => ['nullable','size:0'],
        ];
    }
    public function authorize(): bool { return true; }
}
