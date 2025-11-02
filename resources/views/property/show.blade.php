<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8">
        <h1 class="text-2xl font-bold mt-3 mb-2">{{ $property->name ?? $property->title }}</h1>

        {{-- Licencias turísticas --}}
        @if($property->tourism_license || $property->rental_registration)
            <div class="flex flex-wrap gap-2 mb-3">
                @if($property->tourism_license)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Licencia: {{ $property->tourism_license }}
                    </span>
                @endif
                @if($property->rental_registration)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Registro: {{ $property->rental_registration }}
                    </span>
                @endif
            </div>
        @endif

        @if($property->photos->count())
            {{-- GRID TEMPORAL (se cambiará por carrusel) --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($property->photos->sortBy('sort_order') as $photo)
                    <img src="{{ str_starts_with($photo->url, 'http') ? $photo->url : asset('storage/' . $photo->url) }}"
                        alt="Foto" class="w-full h-48 md:h-56 object-cover rounded-lg" loading="lazy">
                @endforeach
            </div>
        @endif

        {{-- DESCRIPCIÓN --}}
        <div class="bg-white shadow sm:rounded-xl p-5 mt-4">
            <p class="mb-3 text-gray-700">{{ $property->description }}</p>

            <div class="text-sm text-gray-500 mb-4">
                {{ $property->address }}
                @if(!empty($property->postal_code)) · {{ $property->postal_code }}@endif
                @if(!empty($property->city)) · {{ $property->city }}@endif
                @if(!empty($property->province)) ({{ $property->province }})@endif
            </div>

            {{-- MAPA --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Ubicación</h3>
                {{-- altura inline por si tailwind no la aplica --}}
                <div id="map" class="w-full rounded-lg border border-gray-200" style="height: 320px;"></div>
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
                        // punto exacto guardado
                        const loc = { lat: {{ $property->latitude }}, lng: {{ $property->longitude }} };

                        const map = new google.maps.Map(mapEl, {
                            center: loc,
                            zoom: 16,
                            mapId: 'f8dd0379fb4feadfc2e9ae6d',
                        });

                        new google.maps.marker.AdvancedMarkerElement({
                            map,
                            position: loc,
                            title: @json($property->name),
                        });
                    @else
                        // geocodificar dirección (puede no ser exacto)
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

            {{-- FORMULARIO DE RESERVA --}}
            @auth
                <form method="POST" action="{{ route('reservas.store') }}" class="space-y-4">

                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entrada</label>
                            <input type="date" name="check_in" id="check_in" value="{{ old('check_in') }}"
                                min="{{ now()->toDateString() }}" class="mt-1 w-full border rounded p-2">
                            @error('check_in') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Salida</label>
                            <input type="date" name="check_out" id="check_out" value="{{ old('check_out') }}"
                                min="{{ now()->addDay()->toDateString() }}" class="mt-1 w-full border rounded p-2">
                            @error('check_out') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Huéspedes</label>
                            <input type="number" name="guests" min="1" max="{{ $property->capacity }}"
                                value="{{ old('guests', 2) }}" class="mt-1 w-full border rounded p-2">
                            @error('guests') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700 space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-6 h-6 rounded border border-gray-300"
                                    style="background-color: #ffebee;"></span>
                                <span>Fechas no disponibles</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-6 h-6 rounded border border-gray-300"
                                    style="background-color: #e8f5e9;"></span>
                                <span>Fechas disponibles</span>
                            </div>
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                            Reservar
                        </button>
                    </div>
                </form>

                {{-- Script para deshabilitar fechas bloqueadas --}}
                <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const blockedDates = @json($blockedDates);

                        // Configurar Flatpickr para check-in
                        const checkInPicker = flatpickr('#check_in', {
                            locale: 'es',
                            minDate: 'today',
                            dateFormat: 'Y-m-d',
                            disable: blockedDates,
                            onChange: function (selectedDates, dateStr) {
                                // Actualizar min date del check-out
                                if (selectedDates.length > 0) {
                                    const nextDay = new Date(selectedDates[0]);
                                    nextDay.setDate(nextDay.getDate() + 1);
                                    checkOutPicker.set('minDate', nextDay);
                                }
                            },
                            onDayCreate: function (dObj, dStr, fp, dayElem) {
                                const date = dayElem.dateObj.toISOString().split('T')[0];
                                if (blockedDates.includes(date)) {
                                    dayElem.classList.add('flatpickr-disabled');
                                    dayElem.style.backgroundColor = '#ffebee';
                                    dayElem.style.color = '#c62828';
                                } else {
                                    dayElem.style.backgroundColor = '#e8f5e9';
                                }
                            }
                        });

                        // Configurar Flatpickr para check-out
                        const checkOutPicker = flatpickr('#check_out', {
                            locale: 'es',
                            minDate: new Date().fp_incr(1), // mañana
                            dateFormat: 'Y-m-d',
                            disable: blockedDates,
                            onChange: function (selectedDates, dateStr) {
                                // Validar que no haya fechas bloqueadas en el rango
                                const checkIn = checkInPicker.selectedDates[0];
                                const checkOut = selectedDates[0];

                                if (checkIn && checkOut) {
                                    let hasBlocked = false;
                                    const current = new Date(checkIn);

                                    while (current < checkOut) {
                                        const dateStr = current.toISOString().split('T')[0];
                                        if (blockedDates.includes(dateStr)) {
                                            hasBlocked = true;
                                            break;
                                        }
                                        current.setDate(current.getDate() + 1);
                                    }

                                    if (hasBlocked) {
                                        alert('⚠️ El rango seleccionado incluye fechas no disponibles.');
                                        checkOutPicker.clear();
                                    }
                                }
                            },
                            onDayCreate: function (dObj, dStr, fp, dayElem) {
                                const date = dayElem.dateObj.toISOString().split('T')[0];
                                if (blockedDates.includes(date)) {
                                    dayElem.classList.add('flatpickr-disabled');
                                    dayElem.style.backgroundColor = '#ffebee';
                                    dayElem.style.color = '#c62828';
                                } else {
                                    dayElem.style.backgroundColor = '#e8f5e9';
                                }
                            }
                        });
                    });
                </script>
            @else
                {{-- ENLACE A INICIO DE SESIÓN --}}
                <a href="{{ route('login') }}" class="text-indigo-600 underline">Inicia sesión para reservar</a>
            @endauth
        </div>
    </div>
</x-app-layout>