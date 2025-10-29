<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Propiedades') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            {{-- Botón crear nueva propiedad --}}
            <div class="mb-6">
                <a href="{{ route('admin.properties.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear Nueva Propiedad
                </a>
            </div>

            {{-- Grid de propiedades --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @forelse($properties as $property)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 {{ $property->trashed() ? 'border-red-300 bg-red-50' : 'border-gray-200' }}">
                        {{-- Foto de portada --}}
                        <div class="relative">
                            @php
                                $coverPhoto = $property->photos->where('is_cover', true)->first() ?? $property->photos->first();
                            @endphp
                            
                            @if($coverPhoto)
                                <img 
                                    src="{{ str_starts_with($coverPhoto->url, 'http') ? $coverPhoto->url : asset('storage/' . $coverPhoto->url) }}" 
                                    alt="{{ $property->name }}"
                                    class="w-full h-20 object-cover"
                                >
                            @else
                                <div class="w-full h-20 bg-gray-200 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Badge estado --}}
                            @if($property->trashed())
                                <div class="absolute top-2 right-2 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-full">
                                    DADA DE BAJA
                                </div>
                            @else
                                <div class="absolute top-2 right-2 bg-green-600 text-white text-[10px] font-bold px-2 py-1 rounded-full">
                                    ACTIVA
                                </div>
                            @endif
                        </div>

                        {{-- Información --}}
                        <div class="p-2">
                            <h3 class="text-xs font-semibold text-gray-900 mb-1 truncate">{{ $property->name }}</h3>
                            <p class="text-[10px] text-gray-600 mb-1">
                                {{ $property->capacity }} pers. • {{ $property->photos->count() }} fotos
                                @if($property->city)
                                    <br>{{ $property->city }}
                                @endif
                            </p>

                            {{-- Botones de acción --}}
                            <div class="flex flex-col gap-1">
                                @if(!$property->trashed())
                                    <a href="{{ route('admin.properties.dashboard', $property->id) }}" 
                                       class="w-full text-center px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-[10px] font-semibold">
                                        Gestionar
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('admin.properties.restore', $property->id) }}" class="w-full" style="display: block;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="w-full text-center px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-[10px] font-semibold"
                                                style="background-color: #16a34a !important; color: white !important; display: block !important; opacity: 1 !important; visibility: visible !important;"
                                                onclick="return confirm('¿Restaurar esta propiedad?')">
                                            RESTAURAR PROPIEDAD
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay propiedades</h3>
                        <p class="mt-1 text-sm text-gray-500">Comienza creando una nueva propiedad.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
