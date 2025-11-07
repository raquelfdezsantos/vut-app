@extends('layouts.app')

@section('title', 'Staynest – Alojamientos')

@section('content')
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
        <h1 class="font-dmserif" style="font-size: 2rem; line-height: 1.2; margin: 1rem 0 1.25rem 0;">
            Alojamientos
        </h1>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap: 18px;">
            @foreach($properties as $p)
                @php
                    $photos = optional($p->photos)->sortBy('sort_order');
                    $first = $photos?->first();
                    $thumb = $first
                        ? (str_starts_with($first->url ?? '', 'http') ? $first->url : asset('storage/' . ltrim($first->url ?? '', '/')))
                        : null;
                    $gid = 'gallery-' . $p->id;
                @endphp

                <div class="sn-card">
                    <div class="sn-aspect-4-3">
                        @if($thumb)
                            <img src="{{ $thumb }}" alt="Foto {{ $p->name }}" loading="lazy">
                        @endif

                        <div class="sn-fab">
                            <button type="button" onclick="openGallery('{{ $gid }}')" class="sn-btn sn-btn-ghost">Ver
                                fotos</button>
                                <a href="{{ route('properties.show', $p->slug ?? $p->id) }}" class="sn-btn sn-btn-accent">Ver
                                ficha</a>
                        </div>
                    </div>

                    <div style="padding: 14px 16px;">
                        <h2 style="font-size:1.05rem; font-weight:600; color:#fff; margin:0 0 4px 0;">{{ $p->name }}</h2>
                        @if(($p->city ?? null) || ($p->region ?? null))
                            <p style="color:#999; font-size:.9rem; margin:0;">{{ trim(($p->city ?? '') . ' ' . ($p->region ?? '')) }}
                            </p>
                        @endif
                    </div>

                    {{-- Galería oculta para PhotoSwipe (todas las fotos) --}}
                    <div id="{{ $gid }}" style="display:none;">
                        @if($photos)
                            @foreach($photos as $photo)
                                @php
                                    $src = str_starts_with($photo->url ?? '', 'http')
                                        ? $photo->url
                                        : asset('storage/' . ltrim($photo->url ?? '', '/'));
                                    $w = $photo->width ?? 1600;
                                    $h = $photo->height ?? 1067;
                                @endphp
                                <a href="{{ $src }}" data-pswp-width="{{ $w }}" data-pswp-height="{{ $h }}"></a>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection

@push('scripts')
    <script type="module">
        import PhotoSwipeLightbox from 'photoswipe/lightbox';
        import 'photoswipe/style.css';

        const instances = new Map();
        window.openGallery = function (id) {
            if (!instances.has(id)) {
                const lightbox = new PhotoSwipeLightbox({
                    gallery: '#' + id,
                    children: 'a',
                    showHideAnimationType: 'fade',
                    pswpModule: () => import('photoswipe')
                });
                lightbox.init();
                instances.set(id, lightbox);
            }
            document.querySelector('#' + id + ' a')?.click();
        }
    </script>
@endpush