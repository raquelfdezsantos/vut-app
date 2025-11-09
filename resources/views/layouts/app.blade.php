<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Staynest') }}</title>

    <!-- Preload fuentes locales optimizadas (WOFF2) -->
    <link rel="preload" href="/fonts/Source_Serif_4/SourceSerif4-VariableFont_opsz,wght.woff2" as="font"
        type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/Source_Serif_4/SourceSerif4-Italic-VariableFont_opsz,wght.woff2" as="font"
        type="font/woff2" crossorigin>

    <!-- Evitar FOUC del tema: aplicar tema almacenado antes de cargar CSS -->
    <script>
        (function () {
            try {
                var t = localStorage.getItem('sn-theme');
                if (t === 'light' || t === 'dark') {
                    document.documentElement.setAttribute('data-theme', t);
                }
            } catch (e) { /* noop */ }
        })();
    </script>

    <!-- Staynest Styles (cache-busting con filemtime) -->
    <link rel="stylesheet" href="{{ asset('css/staynest.css') }}?v={{ filemtime(public_path('css/staynest.css')) }}">

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
        {{-- Soporte para uso como componente (<x-app-layout>) y como layout con @section --}}
            {{ $slot ?? '' }}
            @yield('content')
    </main>

    <!-- Footer -->
    <footer
        style="background-color: var(--color-bg-secondary); border-top: 1px solid var(--color-border-light); margin-top: var(--spacing-2xl);">
        <div class="container" style="padding-top: var(--spacing-md); padding-bottom: var(--spacing-md);">
            <div class="footer-grid"
                style="display: grid; grid-template-columns: 1fr minmax(260px,320px) 1fr; gap: var(--spacing-md); font-size: var(--text-sm); color: var(--color-text-secondary); align-items: start;">
                {{-- Columna 1: Licencias --}}
                <div class="footer-col-1">
                    <h3
                        style="font-family: var(--font-serif); font-size: var(--text-lg); color: var(--color-text-primary); margin-bottom: var(--spacing-md);">
                        Información Legal</h3>
                    @php
                        $property = \App\Models\Property::first();
                    @endphp
                    @if($property && ($property->tourism_license || $property->rental_registration))
                        @if($property->tourism_license)
                            <p style="margin-bottom: var(--spacing-sm);">
                                <span style="font-weight: 500; color: var(--color-text-primary);">Asturias - Número de registro
                                    autonómico</span><br>
                                {{ $property->tourism_license }}
                            </p>
                        @endif
                        @if($property->rental_registration)
                            <p>
                                <span style="font-weight: 500; color: var(--color-text-primary);">España - Número de registro
                                    nacional</span><br>
                                {{ $property->rental_registration }}
                            </p>
                        @endif
                    @endif
                </div>

                {{-- Columna 2: Enlaces legales --}}
                <div class="footer-col-2" style="margin-inline:auto;">
                    <h3
                        style="font-family: var(--font-serif); font-size: var(--text-lg); color: var(--color-text-primary); margin-bottom: var(--spacing-md);">
                        Legal</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: var(--spacing-xs);"><a href="{{ route('legal.aviso') }}"
                                class="sn-link">Aviso Legal</a></li>
                        <li style="margin-bottom: var(--spacing-xs);"><a href="{{ route('legal.privacidad') }}"
                                class="sn-link">Política de Privacidad</a></li>
                        <li><a href="{{ route('legal.cookies') }}" class="sn-link">Política de Cookies</a></li>
                    </ul>
                </div>

                {{-- Columna 3: Propiedad --}}
                <div class="footer-col-3">
                    @if($property)
                        <div class="footer-prop">
                            <h3
                                style="font-family: var(--font-serif); font-size: var(--text-lg); color: var(--color-text-primary); margin-bottom: var(--spacing-md);">
                                {{ $property->name }}
                            </h3>
                            <p style="color: var(--color-text-secondary);">&copy; {{ date('Y') }} Todos los derechos
                                reservados.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Crédito desarrolladora --}}
            <div
                style="margin-top: var(--spacing-md); padding-top: var(--spacing-sm); border-top: 1px solid var(--color-border-light); text-align: center;">
                <p style="font-size: var(--text-xs); color: var(--color-text-muted);">
                    Desarrollado por <span style="font-weight: 500; color: var(--color-text-secondary);">Raquel
                        Fernández Santos</span> ·
                    <span style="font-weight: 600; color: var(--color-accent);">{{ config('app.name') }}</span>
                </p>
                <p class="footer-cookie-row"
                    style="font-size: var(--text-xs); color: var(--color-text-muted); margin-top: var(--spacing-sm); display:flex; align-items:center; gap:8px; justify-content:center;">
                    <x-icon name="cookie" :size="16" class="footer-cookie-icon" />
                    <span>Este sitio utiliza cookies técnicas necesarias para su funcionamiento.</span>
                    <a href="{{ route('legal.cookies') }}" class="sn-link" style="font-size: var(--text-xs);">Más
                        información</a>
                </p>
                <style>
                    /* Cookie: blanco en dark, negro puro en light */
                    html[data-theme="dark"] .footer-cookie-icon {
                        color: #ffffff;
                    }

                    html[data-theme="light"] .footer-cookie-icon {
                        color: #000000;
                    }

                    /* Alineación columnas footer en desktop */
                    @media (min-width: 768px) {
                        .footer-col-1 {
                            text-align: left;
                        }

                        /* Columna 2 alineada a la izquierda como las demás */
                        .footer-col-2 {
                            display: flex;
                            flex-direction: column;
                            align-items: flex-start;
                        }

                        .footer-col-2 h3 {
                            width: 100%;
                            text-align: left;
                            margin-bottom: var(--spacing-sm);
                        }

                        .footer-col-2 ul {
                            width: auto;
                            text-align: left;
                        }

                        .footer-col-3 {
                            text-align: right;
                        }

                        .footer-col-3 .footer-prop {
                            display: inline-block;
                            text-align: left;
                        }
                    }

                    @media (min-width: 1024px) {

                        /* Centrar columna Legal con ancho controlado */
                        .footer-grid {
                            grid-template-columns: 1fr minmax(260px, 320px) 1fr !important;
                        }

                        .footer-col-2 {
                            margin-inline: auto;
                        }

                        .footer-col-2 {
                            padding: 0;
                        }
                    }

                    @media (max-width: 640px) {
                        .footer-cookie-row {
                            flex-wrap: wrap;
                            flex-direction: row;
                            gap: 6px;
                            justify-content: center;
                        }

                        .footer-cookie-row .footer-cookie-icon {
                            order: 0;
                        }

                        /* Asegurar visibilidad de créditos en móvil */
                        .footer-credit {
                            display: block !important;
                        }
                    }
                </style>
            </div>
        </div>
    </footer>

    @stack('scripts')

</body>

</html>