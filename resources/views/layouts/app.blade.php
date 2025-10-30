<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="font-sans antialiased">
    <div class="bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header) && !($noHeader ?? false))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif


        <!-- Page Content -->
        <main class="{{ ($compactMain ?? false) ? 'py-0' : 'py-4' }}">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-600">
                    {{-- Columna 1: Licencias --}}
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Información Legal</h3>
                        @php
                            $property = \App\Models\Property::first();
                        @endphp
                        @if($property && ($property->tourism_license || $property->rental_registration))
                            @if($property->tourism_license)
                                <p class="mb-1">
                                    <span class="font-medium">Licencia Turística:</span><br>
                                    {{ $property->tourism_license }}
                                </p>
                            @endif
                            @if($property->rental_registration)
                                <p>
                                    <span class="font-medium">Registro de Alquiler:</span><br>
                                    {{ $property->rental_registration }}
                                </p>
                            @endif
                        @endif
                    </div>

                    {{-- Columna 2: Enlaces legales --}}
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Legal</h3>
                        <ul class="space-y-1">
                            <li><a href="{{ route('legal.aviso') }}" class="hover:text-indigo-600">Aviso Legal</a></li>
                            <li><a href="{{ route('legal.privacidad') }}" class="hover:text-indigo-600">Política de Privacidad</a></li>
                            <li><a href="{{ route('legal.cookies') }}" class="hover:text-indigo-600">Política de Cookies</a></li>
                        </ul>
                    </div>

                    {{-- Columna 3: Propiedad --}}
                    <div>
                        @if($property)
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $property->name }}</h3>
                            <p>&copy; {{ date('Y') }} Todos los derechos reservados.</p>
                        @endif
                    </div>
                </div>

                {{-- Crédito desarrolladora --}}
                <div class="mt-6 pt-4 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500">
                        Desarrollado por <span class="font-medium text-gray-700">Raquel Fernández Santos</span> · 
                        <span class="font-semibold text-indigo-600">{{ config('app.name') }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-2">
                        🍪 Este sitio utiliza cookies técnicas necesarias para su funcionamiento. 
                        <a href="{{ route('legal.cookies') }}" class="underline hover:text-gray-600">Más información</a>
                    </p>
                </div>
            </div>
        </footer>

    </div>
</body>

</html>