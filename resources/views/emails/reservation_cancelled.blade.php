<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Reserva cancelada</title></head>
<body style="font-family: Arial, sans-serif; color:#222; line-height:1.5;">
	<h2 style="margin:0 0 12px;">Reserva cancelada</h2>
	<p>Hola {{ $reservation->user->name }},</p>
	<p>Tu reserva en <strong>{{ $reservation->property->name }}</strong> ha sido cancelada correctamente.</p>
	<ul>
		<li><strong>Nº de huéspedes:</strong> {{ $reservation->guests }}</li>
		<li><strong>Fechas:</strong> {{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</li>
		<li><strong>Total:</strong> {{ number_format($reservation->total_price, 2, ',', '.') }} €</li>
	</ul>
	<p>Si tienes dudas, contacta con soporte.</p>
</body>
</html>
