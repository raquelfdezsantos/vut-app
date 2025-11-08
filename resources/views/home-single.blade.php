@extends('layouts.app')

@section('title', $property ? ($property->name . ' – Staynest') : 'Staynest')

@section('content')
<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
    <header style="margin: 1rem 0 1.25rem 0;">
        <h1 class="font-dmserif" style="font-size: 2rem; line-height: 1.2;">
            {{ $property->name ?? 'Staynest' }}
        </h1>
        @if($property && ($property->license ?? null))
            <p style="color:#999; font-size:.9rem; margin-top:.25rem;">
                Licencia: {{ $property->license }}
            </p>
        @endif
    </header>

    {{-- Galería principal custom (Foto grande + 4/6 miniaturas) --}}
@php
  $photos = $property?->photos?->sortBy('sort_order') ?? collect();
  $main = $photos->first();
  $thumbs = $photos->slice(1, 6); // máximo 6
@endphp

@if($photos->count())
<section class="sn-home-gallery" style="margin-bottom: var(--spacing-2xl);">
  <div class="sn-gallery-grid">
    {{-- Imagen principal --}}
    @if($main)
      @php
        $srcMain = str_starts_with($main->url ?? '', 'http') ? $main->url : asset('storage/' . ltrim($main->url ?? '', '/'));
        $wMain = $main->width ?? 1600; $hMain = $main->height ?? 1067;
      @endphp
      <a href="{{ $srcMain }}" data-pswp-width="{{ $wMain }}" data-pswp-height="{{ $hMain }}" class="sn-main-wrapper">
        <img src="{{ $srcMain }}" alt="Foto principal {{ $property->name }}" class="sn-main-photo" loading="lazy">
      </a>
    @endif

    {{-- Miniaturas --}}
    <div class="sn-thumbs-wrapper">
      @foreach($thumbs as $photo)
        @php
          $src = str_starts_with($photo->url ?? '', 'http') ? $photo->url : asset('storage/' . ltrim($photo->url ?? '', '/'));
          $w = $photo->width ?? 1600; $h = $photo->height ?? 1067;
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
  <div style="padding:1.25rem; border:1px dashed #2a2a2a; border-radius:12px; color:#aaa;">Aún no hay fotos en la galería.</div>
</section>
@endif

  {{-- Descripción --}}
  <article style="max-width: 720px; color: var(--color-text-secondary); line-height:1.7;">
        {!! nl2br(e($property->description ?? 'Alojamiento acogedor y minimalista.')) !!}
    </article>
</div>
@endsection

{{-- Inicialización de PhotoSwipe movida a resources/js/app.js --}}
