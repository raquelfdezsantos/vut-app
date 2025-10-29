<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8">
        <h1 class="text-2xl font-bold mt-3 mb-3">{{ $property->name ?? $property->title }}</h1>

        @if($property->photos->count())
            {{-- GRID TEMPORAL (se cambiará por carrusel) --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($property->photos->sortBy('sort_order') as $photo)
                    <img
                        src="{{ str_starts_with($photo->url, 'http') ? $photo->url : asset('storage/' . $photo->url) }}"
                        alt="Foto"
                        class="w-full h-48 md:h-56 object-cover rounded-lg"
                        loading="lazy"
                    >
                @endforeach
            </div>
        @endif

        {{-- DESCRIPCIÓN --}}
        <div class="bg-white shadow sm:rounded-xl p-5 mt-4">
            <p class="mb-3 text-gray-700">{{ $property->description }}</p>

            <div class="text-sm text-gray-500 mb-4">
                {{ $property->address }}
                @if(!empty($property->city)) · {{ $property->city }} @endif
                · Capacidad: {{ $property->capacity }} huéspedes
            </div>

            {{-- FORMULARIO DE RESERVA --}}
            @auth
                <form method="POST" action="{{ route('reservas.store') }}" class="space-y-4">

                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entrada</label>
                            <input 
                                type="date" 
                                name="check_in" 
                                id="check_in"
                                value="{{ old('check_in') }}" 
                                min="{{ now()->toDateString() }}"
                                class="mt-1 w-full border rounded p-2"
                            >
                            @error('check_in') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Salida</label>
                            <input 
                                type="date" 
                                name="check_out" 
                                id="check_out"
                                value="{{ old('check_out') }}" 
                                min="{{ now()->addDay()->toDateString() }}"
                                class="mt-1 w-full border rounded p-2"
                            >
                            @error('check_out') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Huéspedes</label>
                            <input 
                                type="number" 
                                name="guests" 
                                min="1" 
                                max="{{ $property->capacity }}" 
                                value="{{ old('guests', 2) }}" 
                                class="mt-1 w-full border rounded p-2"
                            >
                            @error('guests') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700 space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-6 h-6 rounded border border-gray-300" style="background-color: #ffebee;"></span>
                                <span>Fechas no disponibles</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-6 h-6 rounded border border-gray-300" style="background-color: #e8f5e9;"></span>
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
                    document.addEventListener('DOMContentLoaded', function() {
                        const blockedDates = @json($blockedDates);

                        // Configurar Flatpickr para check-in
                        const checkInPicker = flatpickr('#check_in', {
                            locale: 'es',
                            minDate: 'today',
                            dateFormat: 'Y-m-d',
                            disable: blockedDates,
                            onChange: function(selectedDates, dateStr) {
                                // Actualizar min date del check-out
                                if (selectedDates.length > 0) {
                                    const nextDay = new Date(selectedDates[0]);
                                    nextDay.setDate(nextDay.getDate() + 1);
                                    checkOutPicker.set('minDate', nextDay);
                                }
                            },
                            onDayCreate: function(dObj, dStr, fp, dayElem) {
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
                            onChange: function(selectedDates, dateStr) {
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
                            onDayCreate: function(dObj, dStr, fp, dayElem) {
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
