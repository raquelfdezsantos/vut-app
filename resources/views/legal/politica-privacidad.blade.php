<x-app-layout>
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: var(--spacing-xl) 1rem;">
        <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); margin-bottom: var(--spacing-lg); color: var(--color-text-primary);">Política de Privacidad</h1>
        <div style="color: var(--color-text-secondary); line-height: 1.7;">
                    <h2>1. INFORMACIÓN AL USUARIO</h2>
                    @php
                        $property = \App\Models\Property::first();
                    @endphp
                    <p>
                        {{ $property->name ?? 'Apartamento Nordeste' }}, como Responsable del Tratamiento, le informa que, según lo dispuesto en el Reglamento (UE) 2016/679, de 27 de abril (RGPD), y en la L.O. 3/2018, de 5 de diciembre, de protección de datos y garantía de los derechos digitales (LOPDGDD), trataremos su datos tal y como reflejamos en la presente Política de Privacidad.
                    </p>

                    <h2>2. FINALIDAD DEL TRATAMIENTO DE DATOS</h2>
                    <p>
                        Los datos personales que nos proporciona se tratarán con las siguientes finalidades:
                    </p>
                    <ul>
                        <li><strong>Gestión de reservas:</strong> Para procesar y gestionar sus reservas de alojamiento turístico.</li>
                        <li><strong>Comunicaciones:</strong> Para enviarle confirmaciones de reserva, facturas y comunicaciones relacionadas con su estancia.</li>
                        <li><strong>Cumplimiento legal:</strong> Para cumplir con las obligaciones legales aplicables al sector turístico.</li>
                        <li><strong>Gestión de consultas:</strong> Para atender y responder a sus consultas a través del formulario de contacto.</li>
                    </ul>

                    <h2>3. LEGITIMACIÓN</h2>
                    <p>
                        La base legal para el tratamiento de sus datos personales es:
                    </p>
                    <ul>
                        <li>La ejecución de un contrato de alojamiento turístico.</li>
                        <li>El cumplimiento de obligaciones legales.</li>
                        <li>Su consentimiento explícito para el envío de comunicaciones comerciales (si aplica).</li>
                    </ul>

                    <h2>4. DATOS RECOPILADOS</h2>
                    <p>
                        Los datos personales que podemos recopilar incluyen:
                    </p>
                    <ul>
                        <li>Nombre y apellidos</li>
                        <li>Correo electrónico</li>
                        <li>Teléfono</li>
                        <li>Datos de pago (procesados de forma segura a través de pasarela de pago)</li>
                        <li>Número de huéspedes</li>
                        <li>Fechas de entrada y salida</li>
                    </ul>

                    <h2>5. CONSERVACIÓN DE DATOS</h2>
                    <p>
                        Sus datos se conservarán mientras se mantenga la relación contractual y, posteriormente, durante los plazos legalmente establecidos para el cumplimiento de obligaciones fiscales y contables (mínimo 6 años).
                    </p>

                    <h2>6. DESTINATARIOS</h2>
                    <p>
                        Sus datos no se cederán a terceros, salvo obligación legal. Los datos podrán ser comunicados a:
                    </p>
                    <ul>
                        <li>Administraciones públicas competentes en materia turística.</li>
                        <li>Entidades financieras para la gestión de pagos.</li>
                        <li>Proveedores de servicios de hosting y mantenimiento web.</li>
                    </ul>

                    <h2>7. SUS DERECHOS</h2>
                    <p>
                        Puede ejercer los siguientes derechos sobre sus datos personales:
                    </p>
                    <ul>
                        <li><strong>Acceso:</strong> Conocer qué datos personales tratamos sobre usted.</li>
                        <li><strong>Rectificación:</strong> Solicitar la corrección de datos inexactos.</li>
                        <li><strong>Supresión:</strong> Solicitar la eliminación de sus datos.</li>
                        <li><strong>Oposición:</strong> Oponerse al tratamiento de sus datos.</li>
                        <li><strong>Limitación:</strong> Solicitar la limitación del tratamiento.</li>
                        <li><strong>Portabilidad:</strong> Recibir sus datos en formato estructurado.</li>
                    </ul>
                    <p>
                        Para ejercer sus derechos, puede contactarnos en [correo@email.com]. También tiene derecho a presentar una reclamación ante la Agencia Española de Protección de Datos (<a href="https://www.aepd.es" target="_blank" class="text-indigo-600">www.aepd.es</a>).
                    </p>

                    <h2>8. MEDIDAS DE SEGURIDAD</h2>
                    <p>
                        Hemos adoptado medidas técnicas y organizativas adecuadas para garantizar la seguridad de sus datos personales y evitar su alteración, pérdida, tratamiento o acceso no autorizado.
                    </p>

                    <h2>9. COOKIES</h2>
                    <p>
                        Este sitio web utiliza únicamente cookies técnicas necesarias para su funcionamiento. Para más información, consulte nuestra <a href="{{ route('legal.cookies') }}" class="text-indigo-600 hover:text-indigo-800">Política de Cookies</a>.
                    </p>

                    <h2>10. ACTUALIZACIÓN</h2>
                    <p>
                        Esta Política de Privacidad puede ser actualizada. Le recomendamos revisarla periódicamente.
                    </p>

                    <p class="text-sm text-gray-500 mt-8">
                        Última actualización: {{ date('d/m/Y') }}
                    </p>
        </div>
    </div>
</x-app-layout>
