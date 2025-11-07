@props(['class' => ''])

<a href="{{ route('home') }}" class="nav-logo-link">
    {{-- Logo para modo oscuro (por defecto) --}}
    <img 
        src="{{ asset('images/logos/logo-dark.png') }}" 
        alt="Staynest" 
        class="nav-logo {{ $class }} dark-mode-logo"
        id="logo-dark"
    >
    
    {{-- Logo para modo claro (oculto por defecto) --}}
    <img 
        src="{{ asset('images/logos/logo-light.png') }}" 
        alt="Staynest" 
        class="nav-logo {{ $class }} light-mode-logo hidden"
        id="logo-light"
    >
</a>

<style>
    .nav-logo-link {
        display: inline-block;
        line-height: 0;
    }
    
    .nav-logo {
        height: 48px;
        width: auto;
        transition: opacity var(--transition-fast);
    }
    
    .nav-logo:hover {
        opacity: 0.85;
    }
    
    /* Control de visibilidad según el modo */
    [data-theme="light"] .dark-mode-logo {
        display: none;
    }
    
    [data-theme="light"] .light-mode-logo {
        display: inline-block !important;
    }
    
    [data-theme="dark"] .dark-mode-logo,
    :root:not([data-theme]) .dark-mode-logo {
        display: inline-block;
    }
    
    [data-theme="dark"] .light-mode-logo,
    :root:not([data-theme]) .light-mode-logo {
        display: none;
    }
</style>
