<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Aviso Legal
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 prose prose-sm max-w-none">
                    <h2>1. DATOS IDENTIFICATIVOS</h2>
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

                    <h2>2. OBJETO</h2>
                    <p>
                        El presente aviso legal regula el uso del sitio web {{ config('app.url') }} (en adelante, el "Sitio Web"), del que es titular {{ $property->name ?? 'Apartamento Nordeste' }}.
                    </p>
                    <p>
                        La navegación por el Sitio Web atribuye la condición de usuario del mismo e implica la aceptación plena y sin reservas de todas y cada una de las disposiciones incluidas en este Aviso Legal.
                    </p>

                    <h2>3. CONDICIONES DE ACCESO Y USO</h2>
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

                    <h2>4. PROPIEDAD INTELECTUAL E INDUSTRIAL</h2>
                    <p>
                        Todos los contenidos del Sitio Web, incluyendo, sin carácter limitativo, textos, fotografías, gráficos, imágenes, iconos, tecnología, software, así como su diseño gráfico y códigos fuente, constituyen una obra cuya propiedad pertenece a {{ $property->name ?? 'Apartamento Nordeste' }}, sin que puedan entenderse cedidos al usuario ninguno de los derechos de explotación sobre los mismos más allá de lo estrictamente necesario para el correcto uso del Sitio Web.
                    </p>

                    <h2>5. RESPONSABILIDAD</h2>
                    <p>
                        {{ $property->name ?? 'Apartamento Nordeste' }} no se hace responsable de:
                    </p>
                    <ul>
                        <li>Los daños y perjuicios de toda naturaleza que pudieran derivarse de la falta de disponibilidad o de continuidad del funcionamiento del Sitio Web.</li>
                        <li>La utilidad o rendimiento de los servicios del Sitio Web.</li>
                        <li>Los contenidos, informaciones, comunicaciones, opiniones o manifestaciones de los usuarios del Sitio Web o de terceros.</li>
                    </ul>

                    <h2>6. ENLACES</h2>
                    <p>
                        En el caso de que en el Sitio Web se dispusiesen enlaces o hipervínculos hacia otros sitios de Internet, {{ $property->name ?? 'Apartamento Nordeste' }} no ejercerá ningún tipo de control sobre dichos sitios y contenidos.
                    </p>

                    <h2>7. PROTECCIÓN DE DATOS</h2>
                    <p>
                        Para más información sobre el tratamiento de datos personales, consulte nuestra <a href="{{ route('legal.privacidad') }}" class="text-indigo-600 hover:text-indigo-800">Política de Privacidad</a>.
                    </p>

                    <h2>8. LEGISLACIÓN APLICABLE</h2>
                    <p>
                        El presente aviso legal se rige por la legislación española vigente.
                    </p>

                    <p class="text-sm text-gray-500 mt-8">
                        Última actualización: {{ date('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
