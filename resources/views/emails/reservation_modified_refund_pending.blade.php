<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { background: #fff; padding: 20px; border: 1px solid #e9ecef; border-radius: 5px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef; font-size: 12px; color: #6c757d; }
        .highlight { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Reserva #{{ $reservation->id }} modificada</h2>
        </div>
        
        <div class="content">
            <p>Hola {{ $reservation->user->name }},</p>
            
            <p>Tu reserva <strong>#{{ $reservation->id }}</strong> en <strong>{{ $reservation->property->name }}</strong> ha sido modificada correctamente.</p>
            
            <h3>Nuevos detalles de la reserva:</h3>
            <ul>
                <li><strong>Check-in:</strong> {{ $reservation->check_in->format('d/m/Y') }}</li>
                <li><strong>Check-out:</strong> {{ $reservation->check_out->format('d/m/Y') }}</li>
                <li><strong>Huéspedes:</strong> {{ $reservation->guests }}</li>
                <li><strong>Nuevo total:</strong> {{ number_format($newTotal, 2) }}€</li>
            </ul>
            
            <div class="highlight">
                <p><strong>📢 Devolución pendiente</strong></p>
                <p>El nuevo total de la reserva es inferior al monto ya pagado. Procederemos a tramitar la devolución de:</p>
                <p class="amount">{{ number_format($refundAmount, 2) }}€</p>
                <p>Te enviaremos un correo de confirmación cuando la devolución se haya procesado correctamente.</p>
            </div>
            
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
            
            <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
        </div>
        
        <div class="footer">
            <p>Este es un mensaje automático. Por favor, no respondas a este correo.</p>
        </div>
    </div>
</body>
</html>
