@php($totalProperties = \App\Models\Property::whereNull('deleted_at')->count())

<header class="nav-header">
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
<style>
    /* Usamos padding-top en body como estrategia principal; spacer queda en 0 como fallback */
    .nav-spacer { height: 0; }
    body.sn-has-fixed-header { padding-top: var(--sn-header-h, 0px); }
    .nav-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        z-index: 99999; /* super alto para sobrevolar mapas y overlays */
        background: var(--color-bg-primary);
        border-bottom: 1px solid var(--color-border-light);
    }

    .nav-container {
        position: relative;
        padding-left: var(--spacing-xl);
        padding-right: calc(var(--spacing-xl) + 56px);
    }

    /* espacio simétrico logo/toggle */
    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        color: var(--color-text-primary);
        cursor: pointer;
        padding: var(--spacing-sm);
        z-index: 100000; /* asegurar visibilidad sobre contenido */
    }

    .mobile-menu-toggle svg {
        width: 24px;
        height: 24px;
    }

    .theme-toggle {
        background: none;
        border: 1px solid var(--color-border-light);
        color: var(--color-text-primary);
        cursor: pointer;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: 0;
        font-size: 0;
    }

    .theme-toggle--corner {
        position: absolute;
        top: 50%;
        right: var(--spacing-xl);
        transform: translateY(-50%);
        z-index: 100000; /* asegurar visibilidad sobre contenido */
    }

    .theme-toggle .icon-sun-wrapper,
    .theme-toggle .icon-moon-wrapper {
        transform: scale(.78);
    }

    .theme-toggle:hover {
        border-color: var(--color-accent);
        color: var(--color-accent);
    }

    .theme-toggle .icon,
    .theme-toggle .icon-sun-wrapper,
    .theme-toggle .icon-moon-wrapper {
        width: 18px;
        height: 18px;
        display: none;
        transition: opacity var(--transition-fast), transform var(--transition-fast);
    }

    html[data-theme="dark"] .theme-toggle--corner .icon-sun-wrapper {
        display: inline-flex;
    }

    html[data-theme="light"] .theme-toggle--corner .icon-moon-wrapper {
        display: inline-flex;
    }

    /* Color blanco para iconos en modo oscuro */
    html[data-theme="dark"] .theme-toggle--corner svg {
        color: #ffffff;
    }

    @media (max-width:768px) {
        .nav-menu {
            display: none;
        }

        .nav-menu.is-open {
            display: flex;
            flex-direction: column;
            position: absolute;
            right: var(--spacing-xl);
            top: 68px;
            gap: var(--spacing-sm);
            background-color: var(--color-bg-primary);
            border: 1px solid var(--color-border-light);
            padding: var(--spacing-md);
            border-radius: 8px;
            z-index: 99990; /* por encima del contenido y justo por debajo de los toggles */
        }

        .mobile-menu-toggle {
            display: block;
            position: absolute;
            top: 50%;
            right: calc(var(--spacing-xl) + 44px);
            transform: translateY(-50%);
        }

        /* separación consistente entre toggle y menú */
        .theme-toggle--corner {
            right: var(--spacing-xl);
        }

        /* misma distancia al borde derecho que logo al izquierdo */
        .nav-container {
            padding-left: var(--spacing-xl);
            padding-right: calc(var(--spacing-xl) + 104px);
        }

        /* espacio para ambos botones */
    }

    @media (min-width:1024px) {
        .nav-container {
            padding-right: calc(var(--spacing-xl) + 72px);
        }

        /* evita solape con Login en responsive pc */
    }
</style>
<script>
    (function(){
        function setNavSpacerHeight(){
            try {
                var header = document.querySelector('.nav-header');
                var spacer = document.querySelector('.nav-spacer');
                if(!header || !spacer) return;

                var headerRect = header.getBoundingClientRect();
                var maxHeight = headerRect.height;

                // Incluir elementos posicionados absolutamente dentro del header (p.ej., theme toggle)
                var absEls = header.querySelectorAll('.theme-toggle--corner, .mobile-menu-toggle');
                absEls.forEach(function(el){
                    var r = el.getBoundingClientRect();
                    var bottomWithinHeader = r.bottom - headerRect.top; // cuánto "baja" dentro del header
                    if (bottomWithinHeader > maxHeight) maxHeight = bottomWithinHeader;
                });

                // Pequeño margen de seguridad por antialiasing/zoom
                var h = Math.ceil(maxHeight) + 6;
                // Estrategia principal: desplazar todo el contenido con padding-top del body
                document.body.classList.add('sn-has-fixed-header');
                document.documentElement.style.setProperty('--sn-header-h', h + 'px');
                // Fallback: mantener el spacer, pero sin ocupar espacio adicional
                spacer.style.height = '0px';
            } catch(e) {}
        }

        // Recalcular en eventos clave
        window.addEventListener('load', function(){
            setNavSpacerHeight();
            // Tras carga de fuentes, puede variar la altura
            setTimeout(setNavSpacerHeight, 150);
            setTimeout(setNavSpacerHeight, 400);
        });
        window.addEventListener('resize', function(){
            window.requestAnimationFrame(setNavSpacerHeight);
        });
        window.addEventListener('orientationchange', setNavSpacerHeight);

        // Observar cambios en el header que puedan afectar a su altura (ej. clases, contenido)
        var headerNode = document.querySelector('.nav-header');
        if (headerNode && 'MutationObserver' in window) {
            var mo = new MutationObserver(function(){ setNavSpacerHeight(); });
            mo.observe(headerNode, { attributes:true, childList:true, subtree:true });
        }
    })();
    </script>