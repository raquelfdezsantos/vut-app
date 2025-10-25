<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>{{ $invoice->number }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#222; }
    .header { display:flex; justify-content:space-between; margin-bottom:16px; }
    .box { border:1px solid #ddd; padding:10px; border-radius:6px; }
    table { width:100%; border-collapse:collapse; margin-top:12px; }
    th, td { border:1px solid #eee; padding:8px; text-align:left; }
    th { background:#f7f7f7; }
    .total { text-align:right; font-weight:bold; font-size: 14px; }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <h2 style="margin:0 0 6px;">Factura {{ $invoice->number }}</h2>
      <div>Emitida: {{ optional($invoice->issued_at)->format('d/m/Y') }}</div>
    </div>
    <div class="box">
      <strong>VUT</strong><br>
      Calle Ejemplo 1<br>
      28000 Madrid<br>
      NIF: X1234567Z
    </div>
  </div>

  <div class="box">
    <strong>Cliente</strong><br>
    {{ $invoice->reservation->user->name }}<br>
    {{ $invoice->reservation->user->email }}
  </div>

  <table>
    <thead>
      <tr>
        <th>Concepto</th>
        <th>Fechas</th>
        <th>Importe</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Alojamiento: {{ $invoice->reservation->property->name ?? 'Alojamiento' }}</td>
        <td>{{ $invoice->reservation->check_in->format('d/m/Y') }} → {{ $invoice->reservation->check_out->format('d/m/Y') }}</td>
        <td>{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
      </tr>
    </tbody>
  </table>

  <p class="total">TOTAL: {{ number_format($invoice->amount, 2, ',', '.') }} €</p>
</body>
</html>
