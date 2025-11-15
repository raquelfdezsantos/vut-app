@props(['transparent' => false])

@php($totalProperties = \App\Models\Property::whereNull('deleted_at')->count())

<header class="nav-header {{ $transparent ?? false ? 'nav-header--transparent' : '' }}">

    <nav class="nav-container">
        <x-logo />

        <ul class="nav-menu">
            <li><a href="{{ route('home') }}"
                    class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a></li>
            <li><a href="{{ route('entorno') }}"
                    class="nav-link {{ request()->routeIs('entorno') ? 'active' : '' }}">Entorno</a></li>
            <li><a href="{{ route('contact.create') }}"
                    class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}">Contacto</a></li>
            <li><a href="{{ route('reservar') }}"
                    class="nav-link {{ request()->routeIs('reservar') ? 'active' : '' }}">Reservar</a></li>
            @if($totalProperties > 1)
                <li><a href="{{ route('properties.index') }}"
                        class="nav-link {{ request()->routeIs('properties.index') ? 'active' : '' }}">Propiedades</a></li>
            @endif
            @auth
                @if(auth()->user()->role === 'admin')
                    <li><a href="{{ route('admin.dashboard') }}" class="nav-link">Admin</a></li>
                @else
                    <li><a href="{{ route('reservas.index') }}"
                            class="nav-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}">Mis Reservas</a></li>
                @endif
            @endauth
            @guest
                <li><a href="{{ route('login') }}"
                        class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">Login</a></li>
            @endguest
        </ul>

        <button id="mobile-menu-toggle" class="mobile-menu-toggle" aria-label="Menú">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </nav>

    <button id="theme-toggle" class="theme-toggle theme-toggle--corner" aria-label="Cambiar tema" title="Cambiar tema">
        <span class="icon-sun-wrapper"><x-icon name="sun" :size="18" /></span>
        <span class="icon-moon-wrapper"><x-icon name="moon" :size="18" /></span>
    </button>
</header>

<div class="nav-spacer"></div>
{{-- Estilos inline eliminados: todo vive en public/css/staynest.css --}}

<script>
    (function () {
        function setNavSpacerHeight() {
            try {
                var header = document.querySelector('.nav-header');
                var spacer = document.querySelector('.nav-spacer');
                if (!header || !spacer) return;

                var headerRect = header.getBoundingClientRect();
                var maxHeight = headerRect.height;

                var absEls = header.querySelectorAll('.theme-toggle--corner, .mobile-menu-toggle');
                absEls.forEach(function (el) {
                    var r = el.getBoundingClientRect();
                    var bottomWithinHeader = r.bottom - headerRect.top;
                    if (bottomWithinHeader > maxHeight) maxHeight = bottomWithinHeader;
                });

                var h = Math.ceil(maxHeight) + 6;
                document.body.classList.add('sn-has-fixed-header');
                document.documentElement.style.setProperty('--sn-header-h', h + 'px');
                spacer.style.height = '0px';
            } catch (e) { }
        }

        window.addEventListener('load', function () {
            setNavSpacerHeight();
            setTimeout(setNavSpacerHeight, 150);
            setTimeout(setNavSpacerHeight, 400);
        });
        window.addEventListener('resize', function () {
            window.requestAnimationFrame(setNavSpacerHeight);
        });
        window.addEventListener('orientationchange', setNavSpacerHeight);

        var headerNode = document.querySelector('.nav-header');
        if (headerNode && 'MutationObserver' in window) {
            var mo = new MutationObserver(function () { setNavSpacerHeight(); });
            mo.observe(headerNode, { attributes: true, childList: true, subtree: true });
        }

        const btn = document.getElementById('mobile-menu-toggle');
        const menu = document.querySelector('.nav-menu');
        if (btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('is-open');
                btn.classList.toggle('is-active');
            });
        }

        // Header sólido al hacer scroll (solo Home comienza transparente)
        function updateHeaderMode() {
            var header = document.querySelector('.nav-header');
            if (!header) return;

            var threshold = 40;
            const IS_HOME = @json(request()->routeIs('home'));

            // No-home: siempre sólido, pase lo que pase
            if (!IS_HOME) {
                header.classList.add('nav-header--solid');
                header.classList.remove('nav-header--transparent');
                return;
            }

            // Home: transparente arriba, sólido al hacer scroll
            if (window.scrollY > threshold) {
                header.classList.add('nav-header--solid');
                header.classList.remove('nav-header--transparent');
            } else {
                header.classList.add('nav-header--transparent');
                header.classList.remove('nav-header--solid');
            }
        }

        window.addEventListener('scroll', updateHeaderMode);
        window.addEventListener('load', function () {
            updateHeaderMode();
            setTimeout(updateHeaderMode, 150);
            setTimeout(updateHeaderMode, 500);
        });
        window.addEventListener('resize', function () {
            updateHeaderMode();
            setTimeout(updateHeaderMode, 100);
        });
    })();
</script>