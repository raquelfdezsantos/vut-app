<p>Hola Admin,</p>
<p>Se ha recibido una nueva reserva.</p>
<ul>
  <li>ID: #{{ $reservation->id }}</li>
  <li>Cliente: {{ $reservation->user->name }} ({{ $reservation->user->email }})</li>
  <li>Propiedad: {{ $reservation->property->name }}</li>
  <li>Fechas: {{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</li>
  <li>Total: {{ number_format($reservation->total_price, 2, ',', '.') }} €</li>
  <li>Estado: {{ ucfirst($reservation->status) }}</li>
</ul>
<p>Panel: {{ url('/admin') }}</p>
