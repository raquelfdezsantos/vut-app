<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Pago completado</title></head>
<body style="font-family: Arial, sans-serif; color:#222; line-height:1.5;">
	<h2 style="margin:0 0 12px;">Pago completado</h2>
	<p>Hola {{ $reservation->user->name }},</p>
	<p>Se ha abonado el importe pendiente de tu reserva en <strong>{{ $reservation->property->name }}</strong>.</p>
	<p><strong>Importe abonado:</strong> {{ number_format($amount, 2, ',', '.') }} €</p>
	<p>Gracias por confiar en nosotros.</p>
</body>
</html>
