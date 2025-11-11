@extends('layouts.app')

@section('title', $property ? ($property->name . ' – Staynest') : 'Staynest')

@section('content')
    <div class="sn-reservar py-10">
        <header class="mb-7 text-center">
            <h1 class="text-4xl font-serif mb-4">{{ $property->name ?? 'Staynest' }}</h1>
            @if($property && ($property->license ?? null))
                <p class="text-neutral-300 text-sm -mt-2">Licencia: {{ $property->license }}</p>
            @endif
        </header>

        {{-- Galería principal custom (Foto grande + 4/6 miniaturas) --}}
        @php
            $photos = $property?->photos?->sortBy('sort_order') ?? collect();
            $main = $photos->first();
            $thumbs = $photos->slice(1, 6); // máximo 6
        @endphp

        @if($photos->count())
            <section class="sn-home-gallery-wide" style="margin-bottom: var(--spacing-2xl);">
                <div class="sn-gallery-grid">
                    {{-- Imagen principal --}}
                    @if($main)
                        @php
                            $srcMain = str_starts_with($main->url ?? '', 'http') ? $main->url : asset('storage/' . ltrim($main->url ?? '', '/'));
                            $wMain = $main->width ?? 1600;
                            $hMain = $main->height ?? 1067;
                          @endphp
                        <a href="{{ $srcMain }}" data-pswp-width="{{ $wMain }}" data-pswp-height="{{ $hMain }}"
                            class="sn-main-wrapper">
                            <img src="{{ $srcMain }}" alt="Foto principal {{ $property->name }}" class="sn-main-photo"
                                loading="lazy">
                        </a>
                    @endif

                    {{-- Miniaturas --}}
                    <div class="sn-thumbs-wrapper">
                        @foreach($thumbs as $photo)
                            @php
                                $src = str_starts_with($photo->url ?? '', 'http') ? $photo->url : asset('storage/' . ltrim($photo->url ?? '', '/'));
                                $w = $photo->width ?? 1600;
                                $h = $photo->height ?? 1067;
                            @endphp
                            <a href="{{ $src }}" data-pswp-width="{{ $w }}" data-pswp-height="{{ $h }}" class="sn-thumb-item">
                                <img src="{{ $src }}" alt="Thumbnail {{ $loop->iteration }}" loading="lazy">
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @else
            <section style="margin-bottom: var(--spacing-2xl);">
                <div style="padding:1.25rem; border:1px dashed #2a2a2a; border-radius:12px; color:#aaa;">Aún no hay fotos en la
                    galería.</div>
            </section>
        @endif

        <div class="sn-reading">
            @if($property && ($property->tourism_license || $property->rental_registration))
                <section
                    style="margin: var(--spacing-md) auto var(--spacing-xl); max-width:1200px; background: var(--color-bg-secondary); border:1px solid var(--color-accent); border-radius: var(--radius-base); padding: .95rem 1.25rem; display:grid; grid-template-columns:1fr 1fr; gap:2rem; align-items:start;">
                    <div style="display:flex; flex-direction:column; gap:4px; font-size:.85rem;">
                        <span style="font-weight:600; color: var(--color-text-primary);">Información legal</span>
                        @if($property->tourism_license)
                            <span style="color: var(--color-text-secondary);">Asturias - Número de registro autonómico<br><strong
                                    style="color: var(--color-text-primary); font-weight:600;">{{ $property->tourism_license }}</strong></span>
                        @endif
                    </div>
                    <div style="display:flex; flex-direction:column; gap:4px; font-size:.85rem; text-align:right;">
                        <span style="font-weight:600; color: var(--color-text-primary); visibility:hidden;">Datos del
                            registro</span>
                        @if($property->rental_registration)
                            <span style="color: var(--color-text-secondary);">España - Número de registro nacional<br><strong
                                    style="color: var(--color-text-primary); font-weight:600;">{{ $property->rental_registration }}</strong></span>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Descripción --}}
            <article style="max-width: 720px; color: var(--color-text-secondary); line-height:1.7;">
                {!! nl2br(e($property->description ?? 'Alojamiento acogedor y minimalista.')) !!}
            </article>
        </div>
@endsection
</div>

{{-- Inicialización de PhotoSwipe movida a resources/js/app.js --}}