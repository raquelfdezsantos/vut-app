<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Propiedad') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.properties.store') }}">
                        @csrf

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
                                    value="{{ old('name') }}"
                                    required
                                    maxlength="150"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Slug --}}
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700">
                                    Slug (URL amigable) *
                                </label>
                                <input 
                                    type="text" 
                                    name="slug" 
                                    id="slug"
                                    value="{{ old('slug') }}"
                                    required
                                    maxlength="150"
                                    placeholder="apartamento-centro-madrid"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                <p class="mt-1 text-xs text-gray-500">Se usará en la URL: /propiedad/tu-slug</p>
                                @error('slug')
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
                                >{{ old('description') }}</textarea>
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
                                    value="{{ old('address') }}"
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
                                    value="{{ old('city') }}"
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
                                    value="{{ old('capacity', 2) }}"
                                    required
                                    min="1"
                                    max="50"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('capacity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Botones --}}
                            <div class="flex items-center justify-between pt-4">
                                <a href="{{ route('admin.properties.index') }}" 
                                   class="text-sm text-gray-600 hover:text-gray-900">
                                    ← Cancelar
                                </a>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Crear Propiedad
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-500">
                        <p class="text-sm text-blue-700">
                            <strong>💡 Nota:</strong> Después de crear la propiedad podrás añadir fotos, configurar el calendario de precios y comenzar a recibir reservas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-generar slug desde el nombre
        document.getElementById('name').addEventListener('input', function(e) {
            const name = e.target.value;
            const slug = name
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Quitar acentos
                .replace(/[^a-z0-9\s-]/g, '') // Solo letras, números, espacios y guiones
                .trim()
                .replace(/\s+/g, '-'); // Espacios a guiones
            
            document.getElementById('slug').value = slug;
        });
    </script>
</x-app-layout>
