@extends('layouts.app')

@section('title', 'Contacto')

@section('content')
    <div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-serif mb-3" style="font-weight:400;">Contacto</h1>
            <p class="text-neutral-300 max-w-2xl mx-auto whitespace-normal md:whitespace-nowrap">
                ¿Dudas, consultas o disponibilidad especial? Escríbenos y te responderemos lo antes posible.
            </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 items-stretch">
            <!-- Formulario (estrecho) -->
            <div id="contact-form-column"
                 class="md:col-span-2 lg:col-span-2 space-y-6 max-w-xl flex flex-col">
                @if (session('success'))
                    <div class="mb-4"
                         style="padding: .6rem .75rem; background: #204b23; color:#c6f6d5; border:1px solid #2f6b33; border-radius:6px;">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('contact.store') }}"
                      class="space-y-4"
                      style="display:flex;flex-direction:column;">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name"
                                      name="name"
                                      class="block mt-1 w-full"
                                      :value="old('name')"
                                      required
                                      autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email"
                                      type="email"
                                      name="email"
                                      class="block mt-1 w-full"
                                      :value="old('email')"
                                      required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="subject" value="Asunto" />
                        <x-text-input id="subject"
                                      name="subject"
                                      class="block mt-1 w-full"
                                      :value="old('subject')"
                                      required />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="message" value="Mensaje" />
                        <textarea id="message"
                                  name="message"
                                  rows="6"
                                  class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                                  required>{{ old('message') }}</textarea>
                        <x-input-error :messages="$errors->get('message')" class="mt-2" />
                    </div>

                    <div style="align-self:flex-start; margin-top:.5rem;">
                        <x-primary-button>Enviar</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- MAPA --}}
            <div class="md:col-span-2 lg:col-span-2 space-y-4">
                <h2 class="font-serif text-xl mb-2" style="font-weight:500;">Dónde estamos</h2>
                <div class="text-sm text-neutral-300 leading-relaxed">
                    {{ optional($property)->address ?? 'Dirección no disponible' }}
                    @if(!empty(optional($property)->postal_code)) · {{ optional($property)->postal_code }}@endif
                    @if(!empty(optional($property)->city)) · {{ optional($property)->city }}@endif
                    @if(!empty(optional($property)->province)) ({{ optional($property)->province }})@endif
                </div>

                <div id="map" class="w-full"
                     style="height:460px;border:1px solid var(--color-border-light);border-radius:var(--radius-base);overflow:hidden;"></div>
            </div>

            @php
                $hasCoords = !empty($property->latitude) && !empty($property->longitude);
                $fullAddress = collect([
                    $property->address,
                    $property->postal_code,
                    $property->city,
                    $property->province,
                    'España',
                ])->filter()->implode(', ');
            @endphp

            <script>
                function initMap() {
                    const mapEl = document.getElementById('map');

                    @if($hasCoords)
                        const loc = {
                            lat: Number(@json((string) $property->latitude)),
                            lng: Number(@json((string) $property->longitude))
                        };
                        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

                        const darkStylesBase = [
                            { elementType: 'geometry', stylers: [{ color: '#242f3e' }] },
                            { elementType: 'labels.text.stroke', stylers: [{ color: '#242f3e' }] },
                            { elementType: 'labels.text.fill', stylers: [{ color: '#746855' }] },
                            { featureType: 'administrative.locality', elementType: 'labels.text.fill', stylers: [{ color: '#d59563' }] },
                            { featureType: 'poi', elementType: 'labels.text.fill', stylers: [{ color: '#d59563' }] },
                            { featureType: 'poi.park', elementType: 'geometry', stylers: [{ color: '#263c3f' }] },
                            { featureType: 'poi.park', elementType: 'labels.text.fill', stylers: [{ color: '#6b9a76' }] },
                            { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#38414e' }] },
                            { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ color: '#212a37' }] },
                            { featureType: 'road', elementType: 'labels.text.fill', stylers: [{ color: '#9ca5b3' }] },
                            { featureType: 'road.highway', elementType: 'geometry', stylers: [{ color: '#746855' }] },
                            { featureType: 'road.highway', elementType: 'geometry.stroke', stylers: [{ color: '#1f2835' }] },
                            { featureType: 'road.highway', elementType: 'labels.text.fill', stylers: [{ color: '#f3d19c' }] },
                            { featureType: 'transit', elementType: 'geometry', stylers: [{ color: '#2f3948' }] },
                            { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#17263c' }] },
                            { featureType: 'water', elementType: 'labels.text.fill', stylers: [{ color: '#515c6d' }] },
                            { featureType: 'water', elementType: 'labels.text.stroke', stylers: [{ color: '#17263c' }] }
                        ];

                        const mapIdDefault = @json(config('services.google_maps.map_id'));
                        const mapIdLight   = @json(config('services.google_maps.map_id_light'));
                        const mapIdDark    = @json(config('services.google_maps.map_id_dark'));

                        let mapIdToUse = mapIdDefault;
                        if (isDark) {
                            if (mapIdDark) {
                                mapIdToUse = mapIdDark;
                            } else if (!mapIdDefault && mapIdLight) {
                                mapIdToUse = mapIdLight;
                            }
                        }

                        const mapOptions = {
                            center: loc,
                            zoom: 16
                        };
                        if (mapIdToUse) {
                            mapOptions.mapId = mapIdToUse;
                        }
                        if (isDark && !mapIdDark) {
                            mapOptions.styles = darkStylesBase;
                        }

                        const map = new google.maps.Map(mapEl, mapOptions);

                        if (google.maps.marker?.AdvancedMarkerElement) {
                            new google.maps.marker.AdvancedMarkerElement({
                                map,
                                position: loc,
                                title: @json($property->name)
                            });
                        } else {
                            new google.maps.Marker({
                                map,
                                position: loc,
                                title: @json($property->name)
                            });
                        }

                        const syncMapHeight = () => {
                            const ta = document.getElementById('message');
                            if (!ta) return;
                            const taRect = ta.getBoundingClientRect();
                            const mapRect = mapEl.getBoundingClientRect();
                            const desired = Math.max(260, Math.round(taRect.bottom - mapRect.top));
                            mapEl.style.height = desired + 'px';
                        };
                        syncMapHeight();
                        window.addEventListener('resize', () => requestAnimationFrame(syncMapHeight));

                    @else
                        const address = @json($fullAddress);
                        const geocoder = new google.maps.Geocoder();

                        geocoder.geocode({ address }, (results, status) => {
                            if (status === 'OK' && results[0]) {
                                const loc = results[0].geometry.location;
                                const map = new google.maps.Map(mapEl, {
                                    center: loc,
                                    zoom: 15,
                                    mapId: 'f8dd0379fb4feadfc2e9ae6d',
                                });
                                new google.maps.marker.AdvancedMarkerElement({
                                    map,
                                    position: loc,
                                    title: @json($property->name),
                                });
                            } else {
                                mapEl.innerHTML = 'No se pudo mostrar el mapa.';
                            }
                        });
                    @endif
                }

                (function () {
                    const params = new URLSearchParams({
                        key: "{{ config('services.google_maps.api_key') }}",
                        v: "weekly",
                        libraries: "marker",
                        callback: "initMap",
                        loading: "async",
                    });
                    const s = document.createElement('script');
                    s.src = "https://maps.googleapis.com/maps/api/js?" + params.toString();
                    s.async = true;
                    s.defer = true;
                    document.head.appendChild(s);
                })();
            </script>
        </div>
    </div>
@endsection
