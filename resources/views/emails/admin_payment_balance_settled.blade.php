<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pago de diferencia completado - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .content { background-color: #f9f9f9; padding: 20px; margin-top: 20px; border-radius: 5px; }
        .info { margin: 15px 0; }
        .label { font-weight: bold; color: #555; }
        .amount { font-size: 24px; color: #4CAF50; font-weight: bold; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>💳 Pago de Diferencia Completado</h1>
        <p>Notificación para Admin</p>
    </div>
    
    <div class="content">
        <p>Hola Admin,</p>
        
        <p>Te informamos que el cliente <strong>{{ $reservation->user->name }}</strong> ha completado el pago de la diferencia correspondiente a la reserva modificada.</p>
        
        <div class="info">
            <p class="label">📋 Detalles de la reserva:</p>
            <ul>
                <li><strong>ID Reserva:</strong> #{{ $reservation->id }}</li>
                <li><strong>Propiedad:</strong> {{ $reservation->property->name }}</li>
                <li><strong>Cliente:</strong> {{ $reservation->user->name }} ({{ $reservation->user->email }})</li>
                <li><strong>Check-in:</strong> {{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m/Y') }}</li>
                <li><strong>Check-out:</strong> {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}</li>
            </ul>
        </div>
        
        <div class="info">
            <p class="label">💰 Importe abonado:</p>
            <p class="amount">{{ number_format($amount, 2, ',', '.') }} €</p>
        </div>
        
        <p>El pago se ha procesado correctamente a través de Stripe.</p>
    </div>
    
    <div class="footer">
        <p>Este es un correo automático generado por el sistema VUT.</p>
        <p>{{ config('app.name') }} © {{ date('Y') }}</p>
    </div>
</body>
</html>
