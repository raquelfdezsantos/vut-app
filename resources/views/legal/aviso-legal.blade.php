@extends('layouts.app')

@section('title','Aviso Legal')
@section('content')
<div class="container" style="max-width: 1100px; padding: var(--spacing-xl) 0 var(--spacing-2xl);">
    <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); margin-bottom: var(--spacing-lg); color: var(--color-text-primary);">Aviso Legal</h1>
    <div class="sn-legal" style="color: var(--color-text-secondary); line-height: 1.8; font-size: var(--text-sm);">
                    <h2>1. Datos identificativos</h2>
                    <p>
                        En cumplimiento del artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y Comercio Electrónico, se informa que:
                    </p>
                    <ul>
                        @php
                            $property = \App\Models\Property::first();
                        @endphp
                        <li><strong>Titular:</strong> {{ $property->name ?? 'Apartamento Nordeste' }}</li>
                        <li><strong>CIF/NIF:</strong> [Número de identificación fiscal]</li>
                        <li><strong>Domicilio:</strong> {{ $property->address ?? '[Dirección completa]' }}</li>
                        <li><strong>Email:</strong> [Correo de contacto]</li>
                        <li><strong>Teléfono:</strong> [Número de teléfono]</li>
                        @if($property && $property->tourism_license)
                            <li><strong>Licencia Turística:</strong> {{ $property->tourism_license }}</li>
                        @endif
                        @if($property && $property->rental_registration)
                            <li><strong>Registro de Alquiler:</strong> {{ $property->rental_registration }}</li>
                        @endif
                    </ul>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>2. Objeto</h2>
                    <p>
                        El presente aviso legal regula el uso del sitio web {{ config('app.url') }} (en adelante, el "Sitio Web"), del que es titular {{ $property->name ?? 'Apartamento Nordeste' }}.
                    </p>
                    <p>
                        La navegación por el Sitio Web atribuye la condición de usuario del mismo e implica la aceptación plena y sin reservas de todas y cada una de las disposiciones incluidas en este Aviso Legal.
                    </p>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>3. Condiciones de acceso y uso</h2>
                    <p>
                        El acceso y uso del Sitio Web es gratuito, salvo en lo relativo al coste de conexión a través de la red de telecomunicaciones suministrada por el proveedor de acceso contratado por los usuarios.
                    </p>
                    <p>
                        El usuario se compromete a hacer un uso adecuado de los contenidos y servicios que se ofrecen a través del Sitio Web y a no emplearlos para:
                    </p>
                    <ul>
                        <li>Incurrir en actividades ilícitas, ilegales o contrarias a la buena fe y al orden público.</li>
                        <li>Difundir contenidos o propaganda de carácter racista, xenófobo, pornográfico-ilegal, de apología del terrorismo o atentatorio contra los derechos humanos.</li>
                        <li>Provocar daños en los sistemas físicos y lógicos del Sitio Web, de sus proveedores o de terceras personas.</li>
                    </ul>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>4. Propiedad intelectual e industrial</h2>
                    <p>
                        Todos los contenidos del Sitio Web, incluyendo, sin carácter limitativo, textos, fotografías, gráficos, imágenes, iconos, tecnología, software, así como su diseño gráfico y códigos fuente, constituyen una obra cuya propiedad pertenece a {{ $property->name ?? 'Apartamento Nordeste' }}, sin que puedan entenderse cedidos al usuario ninguno de los derechos de explotación sobre los mismos más allá de lo estrictamente necesario para el correcto uso del Sitio Web.
                    </p>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>5. Responsabilidad</h2>
                    <p>
                        {{ $property->name ?? 'Apartamento Nordeste' }} no se hace responsable de:
                    </p>
                    <ul>
                        <li>Los daños y perjuicios de toda naturaleza que pudieran derivarse de la falta de disponibilidad o de continuidad del funcionamiento del Sitio Web.</li>
                        <li>La utilidad o rendimiento de los servicios del Sitio Web.</li>
                        <li>Los contenidos, informaciones, comunicaciones, opiniones o manifestaciones de los usuarios del Sitio Web o de terceros.</li>
                    </ul>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>6. Enlaces</h2>
                    <p>
                        En el caso de que en el Sitio Web se dispusiesen enlaces o hipervínculos hacia otros sitios de Internet, {{ $property->name ?? 'Apartamento Nordeste' }} no ejercerá ningún tipo de control sobre dichos sitios y contenidos.
                    </p>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>7. Protección de datos</h2>
                    <p>
                        Para más información sobre el tratamiento de datos personales, consulte nuestra <a href="{{ route('legal.privacidad') }}">Política de Privacidad</a>.
                    </p>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>8. Legislación aplicable</h2>
                    <p>
                        El presente aviso legal se rige por la legislación española vigente.
                    </p>

        <p style="margin-top: var(--spacing-xl); font-size: var(--text-xs); color: var(--color-text-muted);">Última actualización: {{ date('d/m/Y') }}</p>
    </div>
</div>
@endsection
