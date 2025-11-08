<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Staynest') }}</title>

    <!-- Preload fuentes locales (Source Serif 4 variable normal e italic) -->
    <link rel="preload" href="/fonts/Source_Serif_4/SourceSerif4-VariableFont_opsz,wght.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/fonts/Source_Serif_4/SourceSerif4-Italic-VariableFont_opsz,wght.ttf" as="font" type="font/ttf" crossorigin>

    <!-- Staynest Styles -->
    <link rel="stylesheet" href="{{ asset('css/staynest.css') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{ $header ?? '' }}

</head>

<body>
    {{-- Navegación pública (si no está autenticado o es vista pública) --}}
    @if(!auth()->check() || request()->routeIs('home', 'properties.*', 'contact.*', 'entorno', 'reservar'))
        <x-nav-public />
    @else
        {{-- Navegación de Laravel Breeze para usuarios autenticados --}}
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($headerSlot) && !($noHeader ?? false))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
                    {{ $headerSlot }}
                </div>
            </header>
        @endif
    @endif

    <!-- Page Content -->
    <main class="{{ ($compactMain ?? false) ? '' : 'container mt-xl' }}">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer
        style="background-color: var(--color-bg-secondary); border-top: 1px solid var(--color-border-light); margin-top: var(--spacing-2xl);">
        <div class="container" style="padding-top: var(--spacing-xl); padding-bottom: var(--spacing-xl);">
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--spacing-xl); font-size: var(--text-sm); color: var(--color-text-secondary);">
                {{-- Columna 1: Licencias --}}
                <div>
                    <h3
                        style="font-family: var(--font-serif); font-size: var(--text-lg); color: var(--color-text-primary); margin-bottom: var(--spacing-md);">
                        Información Legal</h3>
                    @php
                        $property = \App\Models\Property::first();
                    @endphp
                    @if($property && ($property->tourism_license || $property->rental_registration))
                        @if($property->tourism_license)
                            <p style="margin-bottom: var(--spacing-sm);">
                                <span style="font-weight: 500; color: var(--color-text-primary);">Licencia Turística:</span><br>
                                {{ $property->tourism_license }}
                            </p>
                        @endif
                        @if($property->rental_registration)
                            <p>
                                <span style="font-weight: 500; color: var(--color-text-primary);">Registro de
                                    Alquiler:</span><br>
                                {{ $property->rental_registration }}
                            </p>
                        @endif
                    @endif
                </div>

                {{-- Columna 2: Enlaces legales --}}
                <div>
                    <h3
                        style="font-family: var(--font-serif); font-size: var(--text-lg); color: var(--color-text-primary); margin-bottom: var(--spacing-md);">
                        Legal</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: var(--spacing-xs);"><a href="{{ route('legal.aviso') }}"
                                style="color: var(--color-text-secondary); transition: color var(--transition-fast);"
                                onmouseover="this.style.color='var(--color-accent)'"
                                onmouseout="this.style.color='var(--color-text-secondary)'">Aviso Legal</a></li>
                        <li style="margin-bottom: var(--spacing-xs);"><a href="{{ route('legal.privacidad') }}"
                                style="color: var(--color-text-secondary); transition: color var(--transition-fast);"
                                onmouseover="this.style.color='var(--color-accent)'"
                                onmouseout="this.style.color='var(--color-text-secondary)'">Política de Privacidad</a>
                        </li>
                        <li><a href="{{ route('legal.cookies') }}"
                                style="color: var(--color-text-secondary); transition: color var(--transition-fast);"
                                onmouseover="this.style.color='var(--color-accent)'"
                                onmouseout="this.style.color='var(--color-text-secondary)'">Política de Cookies</a></li>
                    </ul>
                </div>

                {{-- Columna 3: Propiedad --}}
                <div>
                    @if($property)
                        <h3
                            style="font-family: var(--font-serif); font-size: var(--text-lg); color: var(--color-text-primary); margin-bottom: var(--spacing-md);">
                            {{ $property->name }}
                        </h3>
                        <p style="color: var(--color-text-secondary);">&copy; {{ date('Y') }} Todos los derechos reservados.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Crédito desarrolladora --}}
            <div
                style="margin-top: var(--spacing-xl); padding-top: var(--spacing-md); border-top: 1px solid var(--color-border-light); text-align: center;">
                <p style="font-size: var(--text-xs); color: var(--color-text-muted);">
                    Desarrollado por <span style="font-weight: 500; color: var(--color-text-secondary);">Raquel
                        Fernández Santos</span> ·
                    <span style="font-weight: 600; color: var(--color-accent);">{{ config('app.name') }}</span>
                </p>
                <p style="font-size: var(--text-xs); color: var(--color-text-muted); margin-top: var(--spacing-sm);">
                    🍪 Este sitio utiliza cookies técnicas necesarias para su funcionamiento.
                    <a href="{{ route('legal.cookies') }}"
                        style="text-decoration: underline; color: var(--color-text-muted);">Más información</a>
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')

</body>

</html>