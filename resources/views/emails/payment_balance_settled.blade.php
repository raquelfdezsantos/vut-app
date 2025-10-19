@component('mail::message')
# Pago completado

Hola {{ $reservation->user->name }},

Se ha abonado el importe pendiente de tu reserva en {{ $reservation->property->name }}.

**Importe abonado:** {{ number_format($amount, 2, ',', '.') }} €

Gracias por confiar en nosotros.

@endcomponent
