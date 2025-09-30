<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8">
        <h1 class="text-2xl font-bold mt-3 mb-3">{{ $property->name ?? $property->title }}</h1>

        @if($property->photos->count())
            {{-- GRID TEMPORAL (luego lo cambiaremos por carrusel) --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($property->photos->sortBy('sort_order') as $photo)
                    <img
                        src="{{ $photo->url }}"
                        alt="Foto"
                        class="w-full h-48 md:h-56 object-cover rounded-lg"
                        loading="lazy"
                    >
                @endforeach
            </div>
        @endif

        <div class="bg-white shadow sm:rounded-xl p-5 mt-4">
            <p class="mb-3 text-gray-700">{{ $property->description }}</p>

            <div class="text-sm text-gray-500 mb-4">
                {{ $property->address }}
                @if(!empty($property->city)) · {{ $property->city }} @endif
                · Capacidad: {{ $property->capacity }} huéspedes
            </div>

            @auth
                <form method="POST" action="{{ route('reservas.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Entrada</label>
                        <input type="date" name="check_in" value="{{ old('check_in') }}" class="mt-1 w-full border rounded p-2">
                        @error('check_in') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Salida</label>
                        <input type="date" name="check_out" value="{{ old('check_out') }}" class="mt-1 w-full border rounded p-2">
                        @error('check_out') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Huéspedes</label>
                        <input type="number" name="guests" min="1" max="{{ $property->capacity }}" value="{{ old('guests', 2) }}" class="mt-1 w-full border rounded p-2">
                        @error('guests') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Reservar
                        </button>
                    </div>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-indigo-600 underline">Inicia sesión para reservar</a>
            @endauth
        </div>
    </div>
</x-app-layout>
