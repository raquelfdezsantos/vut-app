@props(['transparent' => false])

@php($totalProperties = \App\Models\Property::whereNull('deleted_at')->count())

<header class="nav-header {{ $transparent ? 'is-transparent' : '' }}">
  <nav class="nav-container">
    <x-logo />

    <ul class="nav-menu">
      <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a></li>
      <li><a href="{{ route('entorno') }}" class="nav-link {{ request()->routeIs('entorno') ? 'active' : '' }}">Entorno</a></li>
      <li><a href="{{ route('contact.create') }}" class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}">Contacto</a></li>
      <li><a href="{{ route('reservar') }}" class="nav-link {{ request()->routeIs('reservar') ? 'active' : '' }}">Reservar</a></li>
      @if($totalProperties > 1)
        <li><a href="{{ route('properties.index') }}" class="nav-link {{ request()->routeIs('properties.index') ? 'active' : '' }}">Propiedades</a></li>
      @endif
      @auth
        @if(auth()->user()->role === 'admin')
          <li><a href="{{ route('admin.dashboard') }}" class="nav-link">Admin</a></li>
        @else
          <li><a href="{{ route('reservas.index') }}" class="nav-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}">Mis Reservas</a></li>
        @endif
      @endauth
      @guest
        <li><a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">Login</a></li>
      @endguest>
    </ul>

    <button id="mobile-menu-toggle" class="mobile-menu-toggle" aria-label="Menú">
      <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <button id="theme-toggle" class="theme-toggle theme-toggle--corner" aria-label="Cambiar tema" title="Cambiar tema">
      <span class="icon-sun-wrapper"><x-icon name="sun" :size="18" /></span>
      <span class="icon-moon-wrapper"><x-icon name="moon" :size="18" /></span>
    </button>
  </nav>
</header>
<div class="nav-spacer"></div>

<style>
  .nav-spacer{height:0}
  body.sn-has-fixed-header{padding-top:var(--sn-header-h,0px)}

  .nav-header{
    position:fixed; inset:0 auto auto 0; width:100%;
    z-index:9999;
    background: var(--color-bg-primary);
    border-bottom:1px solid var(--color-border-light);
    transition: background-color var(--transition-base), border-color var(--transition-base);
  }
  .nav-header.is-transparent{
    background: transparent;
    border-bottom-color: transparent;
  }

  .nav-container{
    max-width: 1400px;
    margin:0 auto;
    padding: var(--spacing-md) var(--spacing-xl);
    display:flex; align-items:center; justify-content:space-between;
    position:relative;
  }

  .nav-menu{display:flex; align-items:center; gap:var(--spacing-xl); list-style:none}
  .nav-link{font-weight:500; color:var(--color-text-primary); padding:var(--spacing-sm) var(--spacing-md); border-radius:6px}
  .nav-link:hover{color:var(--color-accent); background: rgba(77,141,148,.10)}
  .nav-link.active::after{
    content:''; position:absolute; bottom:-8px; left:50%; transform:translateX(-50%);
    width:30px; height:2px; background:var(--color-accent);
  }

  /* Toggle tema */
  .theme-toggle{background:none; border:1px solid var(--color-border-light); color:var(--color-text-primary);
    width:22px; height:22px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center;
    position:absolute; right:var(--spacing-xl); top:50%; transform:translateY(-50%)
  }
  .theme-toggle .icon-sun-wrapper, .theme-toggle .icon-moon-wrapper{display:none !important}
  html[data-theme="dark"] .theme-toggle .icon-sun-wrapper{display:inline-flex !important}
  html[data-theme="light"] .theme-toggle .icon-moon-wrapper{display:inline-flex !important}

  /* Hamburguesa */
  .mobile-menu-toggle{display:none !important; background:none; border:none; color:var(--color-text-primary);
    position:absolute; right: calc(var(--spacing-xl) + 44px); top:50%; transform:translateY(-50%)
  }

  /* Mobile */
  @media (max-width:768px){
    .nav-menu{display:none}
    .nav-menu.is-open{
      display:flex; flex-direction:column; position:absolute; right:var(--spacing-xl); top:68px;
      gap:var(--spacing-sm); background:var(--color-bg-primary);
      border:1px solid var(--color-border-light); padding:var(--spacing-md); border-radius:8px; z-index:99990;
    }
    .mobile-menu-toggle{display:block !important}
  }
</style>

<script>
  (function(){
    // Altura del header para empujar contenido
    function setNavSpacerHeight(){
      var header=document.querySelector('.nav-header');
      if(!header) return;
      var h=Math.ceil(header.getBoundingClientRect().height)+6;
      document.body.classList.add('sn-has-fixed-header');
      document.documentElement.style.setProperty('--sn-header-h', h+'px');
    }
    window.addEventListener('load', ()=>{ setNavSpacerHeight(); setTimeout(setNavSpacerHeight,150); setTimeout(setNavSpacerHeight,400); });
    window.addEventListener('resize', ()=> requestAnimationFrame(setNavSpacerHeight));

    // Menu móvil
    const btn=document.getElementById('mobile-menu-toggle');
    const menu=document.querySelector('.nav-menu');
    if(btn && menu){ btn.addEventListener('click', ()=> menu.classList.toggle('is-open')); }

    // Toggle tema
    const tbtn=document.getElementById('theme-toggle');
    if(tbtn){
      tbtn.addEventListener('click', ()=>{
        const html=document.documentElement;
        html.setAttribute('data-theme', html.getAttribute('data-theme')==='light' ? 'dark' : 'light');
      });
    }
  })();
</script>
