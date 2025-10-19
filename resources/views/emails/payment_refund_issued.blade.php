<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Devolución emitida</title></head>
<body style="font-family: Arial, sans-serif; color:#222; line-height:1.5;">
	<h2 style="margin:0 0 12px;">Devolución emitida</h2>
	<p>Hola {{ $reservation->user->name }},</p>
	<p>Se ha emitido una devolución de <strong>{{ number_format($refund, 2, ',', '.') }} €</strong> para tu reserva en <strong>{{ $reservation->property->name }}</strong>.</p>
	<p>Si tienes dudas, contacta con soporte.</p>
</body>
</html>
