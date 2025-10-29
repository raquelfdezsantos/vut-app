<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Fotos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Navegación --}}
            <div class="mb-4 flex gap-2">
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1 rounded bg-gray-100 border text-sm hover:bg-gray-200">← Panel</a>
                <a href="{{ route('admin.property.index') }}" class="px-3 py-1 rounded bg-gray-100 border text-sm hover:bg-gray-200">Propiedad</a>
                <a href="{{ route('admin.photos.index') }}" class="px-3 py-1 rounded bg-indigo-100 border border-indigo-300 text-sm font-semibold">Fotos</a>
                <a href="{{ route('admin.calendar.index') }}" class="px-3 py-1 rounded bg-gray-100 border text-sm hover:bg-gray-200">Calendario</a>
            </div>

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            {{-- Formulario de subida --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Subir nuevas fotos</h3>
                    
                    <form method="POST" action="{{ route('admin.photos.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">
                                Selecciona hasta 10 fotos (JPG, PNG, WEBP - máx. 5MB cada una)
                            </label>
                            <input 
                                type="file" 
                                name="photos[]" 
                                id="photos"
                                multiple
                                accept="image/jpeg,image/png,image/webp"
                                required
                                class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100"
                            >
                            @error('photos')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('photos.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button 
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            Subir fotos
                        </button>
                    </form>
                </div>
            </div>

            {{-- Galería de fotos --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Galería ({{ $photos->count() }} fotos)</h3>

                    @if($photos->isEmpty())
                        <p class="text-gray-500 text-center py-8">No hay fotos subidas. Sube la primera foto usando el formulario de arriba.</p>
                    @else
                        <div id="photos-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($photos as $photo)
                                <div class="photo-item border-2 rounded-lg overflow-hidden bg-white shadow-md" data-photo-id="{{ $photo->id }}" style="position: relative; min-height: 280px;">
                                    {{-- Imagen --}}
                                    <img 
                                        src="{{ str_starts_with($photo->url, 'http') ? $photo->url : asset('storage/' . $photo->url) }}" 
                                        alt="Foto {{ $photo->id }}"
                                        class="w-full h-48 object-cover"
                                        style="display: block;"
                                    >

                                    {{-- Badge de portada --}}
                                    @if($photo->is_cover)
                                        <div style="position: absolute; top: 8px; left: 8px; background-color: #eab308; color: white; font-size: 12px; font-weight: 600; padding: 4px 8px; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); z-index: 20;">
                                            ★ Portada
                                        </div>
                                    @endif

                                    {{-- Número de orden --}}
                                    <div style="position: absolute; bottom: 8px; right: 8px; background-color: #1f2937; color: white; font-size: 12px; padding: 4px 8px; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); z-index: 20;">
                                        #{{ $photo->sort_order }}
                                    </div>

                                    {{-- Botones de acción --}}
                                    <div style="position: absolute; top: 8px; right: 8px; display: flex; gap: 4px; z-index: 30;">
                                        {{-- Botón marcar como portada --}}
                                        @if(!$photo->is_cover)
                                            <form method="POST" action="{{ route('admin.photos.set-cover', $photo->id) }}" style="display: inline-block;">
                                                @csrf
                                                <button 
                                                    type="submit"
                                                    title="Marcar como portada"
                                                    style="padding: 6px 10px; background-color: #eab308; color: white; border: none; border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); cursor: pointer; font-size: 14px;"
                                                    onmouseover="this.style.backgroundColor='#ca8a04'"
                                                    onmouseout="this.style.backgroundColor='#eab308'"
                                                >
                                                    ★
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Botón eliminar --}}
                                        <form method="POST" action="{{ route('admin.photos.destroy', $photo->id) }}" style="display: inline-block;" onsubmit="return confirm('¿Eliminar esta foto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit"
                                                title="Eliminar foto"
                                                style="padding: 6px 10px; background-color: #ef4444; color: white; border: none; border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); cursor: pointer; font-size: 14px;"
                                                onmouseover="this.style.backgroundColor='#dc2626'"
                                                onmouseout="this.style.backgroundColor='#ef4444'"
                                            >
                                                🗑️
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Handle para drag --}}
                                    <div class="drag-handle" style="position: absolute; bottom: 0; left: 0; right: 0; height: 40px; background-color: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; cursor: move; font-size: 20px; font-weight: bold; box-shadow: 0 -2px 8px rgba(0,0,0,0.2); z-index: 25;" title="Arrastra para reordenar">
                                        ⠿ ARRASTRAR ⠿
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="text-sm text-gray-500 mt-4">
                            💡 <strong>Tip:</strong> Arrastra las fotos desde la barra azul inferior "ARRASTRAR" para reordenarlas. Haz clic en ★ para marcar como portada o 🗑️ para eliminar.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($photos->isNotEmpty())
    <style>
        .sortable-ghost {
            opacity: 0.4;
            background-color: #e0f2fe;
        }
        .sortable-drag {
            opacity: 1;
            background-color: #dbeafe;
            transform: scale(1.05);
        }
        .sortable-chosen {
            border: 3px solid #3b82f6 !important;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const grid = document.getElementById('photos-grid');
            
            if (!grid) {
                console.error('Grid not found');
                return;
            }

            // Verificar que SortableJS está cargado
            if (typeof Sortable === 'undefined') {
                console.error('SortableJS not loaded');
                alert('Error: La librería de arrastre no se cargó correctamente. Recarga la página.');
                return;
            }
            
            // Inicializar SortableJS para drag & drop
            const sortable = new Sortable(grid, {
                animation: 200,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                chosenClass: 'sortable-chosen',
                forceFallback: true,
                
                onStart: function(evt) {
                    console.log('Drag started');
                },
                
                onEnd: function(evt) {
                    console.log('Drag ended');
                    
                    // Obtener nuevo orden
                    const photoItems = grid.querySelectorAll('.photo-item');
                    const newOrder = Array.from(photoItems).map(item => {
                        const id = item.dataset.photoId;
                        console.log('Photo ID:', id);
                        return id;
                    });
                    
                    console.log('New order:', newOrder);
                    
                    // Enviar al servidor
                    fetch('{{ route("admin.photos.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: newOrder })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Server response:', data);
                        if (data.success) {
                            // Recargar para actualizar números de orden
                            location.reload();
                        } else {
                            alert('Error al reordenar las fotos');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al guardar el nuevo orden. Recarga la página.');
                    });
                }
            });
            
            console.log('Sortable initialized successfully');
        });
    </script>
    @endif
</x-app-layout>
