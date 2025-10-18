<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Pago confirmado</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.5; color:#222;">
  <h2 style="margin:0 0 12px;">Pago confirmado</h2>
  <p>Hola {{ $reservation->user->name }},</p>
  <p>Hemos recibido tu pago. Estos son los detalles:</p>

  <table cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
    <tr>
      <td><strong>Factura:</strong></td>
      <td>{{ $invoice->number }}</td>
    </tr>
    <tr>
      <td><strong>Propiedad:</strong></td>
      <td>{{ $reservation->property->name ?? 'Alojamiento' }}</td>
    </tr>
    <tr>
      <td><strong>Fechas:</strong></td>
      <td>{{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</td>
    </tr>
    <tr>
      <td><strong>Importe:</strong></td>
      <td>{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
    </tr>
    <tr>
      <td><strong>Estado:</strong></td>
      <td>{{ ucfirst($reservation->status) }}</td>
    </tr>
  </table>

  <p style="margin:16px 0 8px;">Puedes ver la factura aquí:</p>
  <p>
    <a href="{{ route('invoices.show', $invoice->number) }}"
       style="display:inline-block; padding:10px 14px; background:#4F46E5; color:white; text-decoration:none; border-radius:6px;">
      Ver factura
    </a>
  </p>

  <p style="margin-top:16px;">Gracias por tu reserva.</p>
</body>
</html>
