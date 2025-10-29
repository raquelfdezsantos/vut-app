<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Propiedad') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Navegación --}}
            <div class="mb-4 flex gap-2">
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1 rounded bg-gray-100 border text-sm hover:bg-gray-200">← Panel</a>
                <a href="{{ route('admin.property.index') }}" class="px-3 py-1 rounded bg-indigo-100 border border-indigo-300 text-sm font-semibold">Propiedad</a>
                <a href="{{ route('admin.photos.index') }}" class="px-3 py-1 rounded bg-gray-100 border text-sm hover:bg-gray-200">Fotos</a>
                <a href="{{ route('admin.calendar.index') }}" class="px-3 py-1 rounded bg-gray-100 border text-sm hover:bg-gray-200">Calendario</a>
            </div>

            {{-- Mensajes de éxito/error --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            {{-- Alerta si la propiedad está dada de baja --}}
            @if($property->trashed())
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                    <p class="font-semibold">⚠️ Esta propiedad está dada de baja</p>
                    <p class="text-sm mt-1">Fue eliminada el {{ $property->deleted_at->format('d/m/Y H:i') }}</p>
                </div>
            @endif

            {{-- Formulario de edición --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Editar Propiedad</h3>

                    <form method="POST" action="{{ route('admin.property.update', $property->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            {{-- Nombre --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Nombre de la propiedad *
                                </label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="name"
                                    value="{{ old('name', $property->name) }}"
                                    required
                                    maxlength="150"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Descripción --}}
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Descripción
                                </label>
                                <textarea 
                                    name="description" 
                                    id="description"
                                    rows="5"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >{{ old('description', $property->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Dirección --}}
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700">
                                    Dirección
                                </label>
                                <input 
                                    type="text" 
                                    name="address" 
                                    id="address"
                                    value="{{ old('address', $property->address) }}"
                                    maxlength="200"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Ciudad --}}
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">
                                    Ciudad
                                </label>
                                <input 
                                    type="text" 
                                    name="city" 
                                    id="city"
                                    value="{{ old('city', $property->city) }}"
                                    maxlength="100"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Capacidad --}}
                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700">
                                    Capacidad (huéspedes) *
                                </label>
                                <input 
                                    type="number" 
                                    name="capacity" 
                                    id="capacity"
                                    value="{{ old('capacity', $property->capacity) }}"
                                    required
                                    min="1"
                                    max="20"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('capacity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Botón de guardar --}}
                            <div class="flex items-center justify-between">
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Guardar cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Zona de peligro: Dar de baja propiedad --}}
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-red-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-red-600 mb-4">⚠️ Zona de peligro</h3>
                    
                    <p class="text-sm text-gray-700 mb-4">
                        Una vez que des de baja la propiedad, se cancelarán todas las reservas futuras activas 
                        y se procesarán los reembolsos automáticamente.
                    </p>

                    @if($futureReservationsCount > 0)
                        <div class="mb-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700">
                            <p class="font-semibold">{{ $futureReservationsCount }} reserva(s) futura(s) será(n) cancelada(s)</p>
                            <p class="text-sm mt-1">Los clientes recibirán un reembolso completo y un email de notificación.</p>
                        </div>
                    @endif

                    <form 
                        method="POST" 
                        action="{{ route('admin.property.destroy', $property->id) }}"
                        onsubmit="return confirmDelete(event, {{ $futureReservationsCount }})"
                    >
                        @csrf
                        @method('DELETE')

                        <button 
                            type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        >
                            Dar de baja propiedad
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(event, reservationsCount) {
            event.preventDefault();
            
            let message = reservationsCount > 0 
                ? `⚠️ ATENCIÓN: Hay ${reservationsCount} reserva(s) activa(s).\n\nSe cancelarán TODAS las reservas futuras y se procesarán reembolsos automáticamente.\n\n¿Estás SEGURO de que quieres dar de baja esta propiedad?`
                : '¿Estás seguro de que quieres dar de baja esta propiedad?';
            
            const firstConfirm = confirm(message);
            if (!firstConfirm) return false;
            
            const secondConfirm = confirm('Esta acción es reversible desde la base de datos.\n\n¿Confirmas que deseas continuar?');
            if (secondConfirm) {
                event.target.submit();
            }
            
            return false;
        }
    </script>
</x-app-layout>
