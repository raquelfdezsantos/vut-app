@component('mail::message')
# Pago completado (Admin)

Se ha abonado el importe pendiente de la reserva #{{ $reservation->id }} ({{ $reservation->property->name }})

**Cliente:** {{ $reservation->user->name }}
**Importe abonado:** {{ number_format($amount, 2, ',', '.') }} €

@endcomponent
