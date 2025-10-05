<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            Propiedades
        </h2>
    </x-slot>

    {{-- FILTROS --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if($properties->isEmpty())
            <div class="bg-white p-6 rounded shadow">
                <p>No hay propiedades aún.</p>
            </div>
        @else
            {{-- LISTADO DE PROPIEDADES --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($properties as $p)
                    <div class="bg-white rounded-xl shadow p-4">
                        {{-- ENLACE A LA FICHA --}}
                        <a href="{{ route('properties.show', $p->slug) }}">
                            @if($p->photos->count()) 
                                <img
                                    src="{{ $p->photos->sortBy('sort_order')->first()->url }}"
                                    alt="{{ $p->name ?? $p->title }}"
                                    class="rounded-lg w-full h-40 object-cover mb-3"
                                >
                            @endif
                            <h3 class="text-lg font-semibold">
                                {{ $p->name ?? $p->title }}
                            </h3>
                        </a>
                        {{-- DESCRIPCIÓN --}}
                        @if(!empty($p->description))
                            <p class="text-gray-600 text-sm mt-1">
                                {{  Str::limit($p->description, 80) }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
