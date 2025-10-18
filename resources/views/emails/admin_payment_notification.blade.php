<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Pago recibido</title></head>
<body style="font-family: Arial, sans-serif; color:#222; line-height:1.5;">
  <h2 style="margin:0 0 12px;">Pago confirmado por el cliente</h2>
  <table cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
    <tr><td><strong>Factura:</strong></td><td>{{ $invoice->number }}</td></tr>
    <tr><td><strong>Cliente:</strong></td><td>{{ $reservation->user->name }} ({{ $reservation->user->email }})</td></tr>
    <tr><td><strong>Alojamiento:</strong></td><td>{{ $reservation->property->name ?? 'Alojamiento' }}</td></tr>
    <tr><td><strong>Fechas:</strong></td><td>{{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</td></tr>
    <tr><td><strong>Importe:</strong></td><td>{{ number_format($invoice->amount, 2, ',', '.') }} €</td></tr>
    <tr><td><strong>Estado reserva:</strong></td><td>{{ ucfirst($reservation->status) }}</td></tr>
    <tr><td><strong>Ver factura:</strong></td>
      <td><a href="{{ route('invoices.show', $invoice->number) }}">Abrir</a></td>
    </tr>
  </table>
</body>
</html>
