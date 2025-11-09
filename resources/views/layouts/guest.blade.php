<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Staynest') }} – Acceso</title>
    <!-- Evitar FOUC del tema: aplicar tema almacenado antes de cargar CSS -->
    <script>
        (function() {
            try {
                var t = localStorage.getItem('sn-theme');
                if (t === 'light' || t === 'dark') {
                    document.documentElement.setAttribute('data-theme', t);
                }
            } catch (e) { /* noop */ }
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/staynest.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background: var(--color-bg-primary); color: var(--color-text-primary);">
    <x-nav-public />
    <main class="sn-auth" style="max-width:480px; margin: 60px auto 0 auto; padding: 0 var(--spacing-md);">
        <section style="padding: var(--spacing-xl);">
            <header style="margin-bottom: var(--spacing-lg); text-align:center;">
                <h1 style="font-family: var(--font-serif); font-size: var(--text-2xl); font-weight:400;">Acceso</h1>
                <p style="color: var(--color-text-secondary); font-size: var(--text-sm); margin-top: var(--spacing-xs);">Inicia sesión para gestionar tu estancia</p>
            </header>
            <div>
                {{ $slot }}
            </div>
        </section>
    </main>
    <footer style="margin-top: var(--spacing-2xl); text-align:center; font-size: var(--text-xs); color: var(--color-text-muted); padding: var(--spacing-lg) 0;">
        &copy; {{ date('Y') }} {{ config('app.name') }} · Acceso seguro · <a href="{{ route('legal.cookies') }}" class="sn-link" style="font-size: var(--text-xs);">Cookies</a>
    </footer>
</body>
</html>
