@extends('layouts.app')

@section('title', ($property->name ?? 'Staynest') . ' – Staynest')

@php $transparentHeader = true; @endphp

@section('content')
    @php
        // Fotos ordenadas; cogemos la primera para el hero o usamos fallback
        $photos = ($property?->photos?->sortBy('sort_order')) ?? collect();
        $first  = $photos->first();
        $hero   = ($first && !empty($first->url))
                    ? (str_starts_with($first->url, 'http') ? $first->url : asset('storage/' . ltrim($first->url, '/')))
                    : 'https://picsum.photos/1600/900';
        $morePhotos = $photos->slice(1, 8);
    @endphp

    {{-- HERO principal: flush top bajo header transparente (clase sn-hero--flush-top) --}}
    <section class="sn-hero sn-hero--flush-top" style="--hero-img: url('{{ $hero ?? 'https://picsum.photos/1600/900' }}')">

        <div class="sn-hero__overlay"></div>

        <div class="container sn-hero__content">
            <div>
                <h1 class="sn-hero__title">{{ $property->name ?? 'Staynest' }}</h1>
                <div class="sn-hero__strap">
                    {{ $property->short_tagline ?? 'Tu escapada perfecta, todo el año.' }}
                </div>
            </div>

            @if($property && ($property->tourism_license || $property->rental_registration))
                <div class="sn-hero__panel-wrap">
                    <div class="sn-hero__panel">
                        <div>
                            <small>Asturias — Registro autonómico</small>
                            <strong>{{ $property->tourism_license ?? '—' }}</strong>
                        </div>
                        <div>
                            <small>España — Registro nacional</small>
                            <strong>{{ $property->rental_registration ?? '—' }}</strong>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="sn-hero__blend"></div>
    </section>

    {{-- DESCRIPCIÓN (mismo ancho que Entorno) --}}
    <section class="sn-reading" style="margin-top: var(--spacing-xl);">
        <article style="color: var(--color-text-secondary); line-height:1.7;">
            {!! nl2br(e($property->description ?? 'Alojamiento acogedor y minimalista.')) !!}
        </article>
    </section>

    {{-- GALERÍA compacta (sin B/N) --}}
    @if(($morePhotos ?? collect())->count() > 0)
        <section class="sn-reading" style="margin-top: var(--spacing-xl);">
            <div class="sn-gallery-compact"
                 style="display:grid; gap:10px; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));">
                @foreach(($morePhotos ?? collect()) as $p)
                    @php
                        $src = (!empty($p->url) && str_starts_with($p->url, 'http'))
                                ? $p->url
                                : asset('storage/' . ltrim($p->url ?? '', '/'));
                        $w = $p->width ?? 1600;
                        $h = $p->height ?? 1067;
                    @endphp
                    <a href="{{ $src }}" data-pswp-width="{{ $w }}" data-pswp-height="{{ $h }}">
                        <img src="{{ $src }}" alt="Foto {{ $loop->iteration }}" loading="lazy"
                             style="width:100%; height:160px; object-fit:cover; border-radius: var(--radius-base);">
                    </a>
                @endforeach
            </div>
            <div style="text-align:center; margin-top:1rem;">
                <button type="button" class="inline-flex items-center px-5 py-2 bg-[color:var(--color-accent)] text-white font-semibold text-sm hover:bg-[color:var(--color-accent-hover)] transition ease-in-out duration-150 focus:outline-none focus:ring-1 focus:ring-[color:var(--color-accent)] focus:ring-offset-1" style="border-radius: 2px;"
                        onclick="document.querySelector('.sn-gallery-compact a')?.click()">
                    Ver galería
                </button>
            </div>
        </section>
    @endif
@endsection

