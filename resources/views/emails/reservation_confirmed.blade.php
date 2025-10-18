<p>Hola {{ $reservation->user->name }},</p>
<p>Hemos registrado tu reserva en <strong>{{ $reservation->property->name }}</strong>.</p>
<ul>
  <li>Check-in: {{ $reservation->check_in->format('d/m/Y') }}</li>
  <li>Check-out: {{ $reservation->check_out->format('d/m/Y') }}</li>
  <li>Huéspedes: {{ $reservation->guests }}</li>
  <li>Total: {{ number_format($reservation->total_price, 2, ',', '.') }} €</li>
  <li>Estado: {{ ucfirst($reservation->status) }}</li>
</ul>
<p>Podrás completar el pago desde “Mis reservas”.</p>
<p>Gracias, <br>VUT App</p>
