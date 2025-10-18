<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Reserva registrada</title></head>
<body style="font-family: Arial, sans-serif; color:#222; line-height:1.5;">
  <h2 style="margin:0 0 12px;">¡Tu reserva se ha registrado!</h2>
  <p>Hola {{ $reservation->user->name }},</p>
  <p>Hemos recibido tu solicitud de reserva. Detalles:</p>

  <table cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
    <tr><td><strong>Reserva:</strong></td><td>#{{ $reservation->id }}</td></tr>
    <tr><td><strong>Alojamiento:</strong></td><td>{{ $reservation->property->name ?? 'Alojamiento' }}</td></tr>
    <tr><td><strong>Entrada:</strong></td><td>{{ $reservation->check_in->format('d/m/Y') }}</td></tr>
    <tr><td><strong>Salida:</strong></td><td>{{ $reservation->check_out->format('d/m/Y') }}</td></tr>
    <tr><td><strong>Huéspedes:</strong></td><td>{{ $reservation->guests }}</td></tr>
    <tr><td><strong>Total:</strong></td><td>{{ number_format($reservation->total_price, 2, ',', '.') }} €</td></tr>
    <tr><td><strong>Estado:</strong></td><td>{{ ucfirst($reservation->status) }}</td></tr>
  </table>

  <p style="margin-top:16px;">Cuando completes el pago te enviaremos la factura automáticamente.</p>
  <p>Gracias por tu reserva.</p>
</body>
</html>
