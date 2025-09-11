<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Debe estar logueado (cliente)
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'check_in'    => ['required', 'date', 'after_or_equal:today'],
            'check_out'   => ['required', 'date', 'after:check_in'],
            'guests'      => ['required', 'integer', 'min:1', 'max:4'], 
        ];
    }

    public function messages(): array
    {
        return [
            'check_out.after' => 'La fecha de salida debe ser posterior a la de entrada.',
        ];
    }
}
