@php
    // Contar propiedades activas para mostrar/ocultar menú "Propiedades"
    $totalProperties = \App\Models\Property::whereNull('deleted_at')->count();
@endphp

<header class="nav-header">
    <nav class="nav-container">
        {{-- Logo --}}
        <x-logo />
        
        {{-- Menú de navegación --}}
        <ul class="nav-menu">
            <li>
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    Inicio
                </a>
            </li>
            <li>
                <a href="{{ route('entorno') }}" class="nav-link {{ request()->routeIs('entorno') ? 'active' : '' }}">
                    Entorno
                </a>
            </li>
            <li>
                <a href="{{ route('contact.create') }}" class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}">
                    Contacto
                </a>
            </li>
            <li>
                <a href="{{ route('reservar') }}" class="nav-link {{ request()->routeIs('reservar') ? 'active' : '' }}">
                    Reservar
                </a>
            </li>
            
            @if($totalProperties > 1)
                {{-- Solo mostrar si hay más de 1 propiedad --}}
                <li>
                    <a href="{{ route('properties.index') }}" class="nav-link {{ request()->routeIs('properties.index') ? 'active' : '' }}">
                        Propiedades
                    </a>
                </li>
            @endif
            
            <li>
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            Admin
                        </a>
                    @else
                        <a href="{{ route('reservas.index') }}" class="nav-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}">
                            Mis Reservas
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">
                        Login
                    </a>
                @endauth
            </li>
        </ul>
        
        {{-- Botón menú hamburguesa (móvil) --}}
        <button id="mobile-menu-toggle" class="mobile-menu-toggle" aria-label="Menú">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </nav>
</header>

{{-- Espaciador para que el contenido no quede bajo el header fijo --}}
<div class="nav-spacer" style="height: 80px;"></div>

<style>
    /* Menú hamburguesa oculto en escritorio */
    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        color: var(--color-text-primary);
        cursor: pointer;
        padding: var(--spacing-sm);
    }
    
    .mobile-menu-toggle svg {
        width: 24px;
        height: 24px;
    }
    
    @media (max-width: 768px) {
        .nav-menu {
            display: none;
            /* TODO: Implementar menú desplegable móvil */
        }
        
        .mobile-menu-toggle {
            display: block;
        }
    }
</style>
