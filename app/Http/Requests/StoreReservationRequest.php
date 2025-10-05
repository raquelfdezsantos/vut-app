<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Request de validación de reservas.
 *
 * Contiene las reglas necesarias para validar las solicitudes de reserva:
 * fechas válidas, capacidad máxima, estancia mínima y disponibilidad del alojamiento.
 */
class StoreReservationRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar la reserva.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Solo los usuarios autenticados pueden realizar reservas.
        return Auth::check();
    }
    
    /**
     * Reglas de validación aplicadas a la solicitud de reserva.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'check_in'    => ['required', 'date', 'after_or_equal:today'],
            'check_out'   => ['required', 'date', 'after:check_in'],
            'guests'      => ['required', 'integer', 'min:1', 'max:4'], 
        ];
    }

    /**
     * Mensajes de error personalizados para las reglas de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'check_out.after' => 'La fecha de salida debe ser posterior a la de entrada.',
        ];
    }
}
