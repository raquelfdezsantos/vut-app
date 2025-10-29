<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Propiedad dada de baja</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #dc2626;">✓ Propiedad dada de baja correctamente</h2>
        
        <p>Hola,</p>
        
        <p>Tu propiedad <strong>{{ $propertyName }}</strong> ha sido dada de baja exitosamente.</p>
        
        @if($cancelledReservations > 0)
            <div style="background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #991b1b;">Resumen de cancelaciones</h3>
                <ul style="margin: 10px 0;">
                    <li><strong>Reservas canceladas:</strong> {{ $cancelledReservations }}</li>
                    <li><strong>Total reembolsado:</strong> {{ number_format($totalRefunded, 2, ',', '.') }} €</li>
                </ul>
                <p style="margin-bottom: 0; font-size: 14px; color: #6b7280;">
                    Los clientes afectados recibirán una notificación por correo electrónico con los detalles de la cancelación y el reembolso.
                </p>
            </div>
        @else
            <p style="color: #059669;">No había reservas futuras activas para esta propiedad.</p>
        @endif
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">
        
        <p style="font-size: 14px; color: #6b7280;">
            Esta acción es reversible desde la base de datos si necesitas recuperar la propiedad.
        </p>
        
        <p style="margin-top: 30px;">
            Saludos,<br>
            <strong>{{ config('app.name') }}</strong>
        </p>
    </div>
</body>
</html>
