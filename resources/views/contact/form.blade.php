
<x-app-layout :compactMain="true">
    <div class="w-full mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Contacto</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Formulario a la izquierda -->
            <div class="p-6">
                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('contact.store') }}">
                    @csrf
                    <label class="block mb-2 text-sm">Nombre</label>
                    <input name="name" value="{{ old('name') }}" class="w-full border rounded p-2 mb-2" required>
                    @error('name') <p class="text-red-600 text-sm mb-2">{{ $message }}</p> @enderror
                    <label class="block mb-2 text-sm">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded p-2 mb-2" required>
                    @error('email') <p class="text-red-600 text-sm mb-2">{{ $message }}</p> @enderror
                        <label class="block mb-2 text-sm">Asunto</label>
                        <input name="subject" value="{{ old('subject') }}" class="w-full border rounded p-2 mb-2" required>
                        @error('subject') <p class="text-red-600 text-sm mb-2">{{ $message }}</p> @enderror
                        <label class="block mb-2 text-sm">Mensaje</label>
                        <textarea name="message" rows="6" class="w-full border rounded p-2 mb-3" required>{{ old('message') }}</textarea>
                        @error('message') <p class="text-red-600 text-sm mb-3">{{ $message }}</p> @enderror
                        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Enviar</button>
                </form>
            </div>
            <!-- Mapa y dirección a la derecha -->
            <div class="p-6 flex flex-col gap-4">
                <div>
                    <h2 class="text-lg font-semibold mb-2">Dónde estamos</h2>
                    <div class="text-sm text-gray-700 mb-2">
                        {{ optional($property)->address ?? 'Dirección no disponible' }}
                        @if(!empty(optional($property)->postal_code)) · {{ optional($property)->postal_code }}@endif
                        @if(!empty(optional($property)->city)) · {{ optional($property)->city }}@endif
                        @if(!empty(optional($property)->province)) ({{ optional($property)->province }})@endif
                    </div>
                </div>
                <div>
                    <div id="map" class="w-full" style="height: 480px;"></div>
                </div>
                @php
                    $hasCoords = !empty(optional($property)->latitude) && !empty(optional($property)->longitude);
                @endphp
                <script>
                    function initMap() {
                        const mapEl = document.getElementById('map');
                        @if($hasCoords)
                            const loc = { lat: {{ optional($property)->latitude }}, lng: {{ optional($property)->longitude }} };
                            const map = new google.maps.Map(mapEl, {
                                center: loc,
                                zoom: 15
                            });
                            new google.maps.Marker({ position: loc, map: map });
                        @else
                            mapEl.innerHTML = '<p class="text-gray-500">Coordenadas no disponibles.</p>';
                        @endif
                    }
                </script>
                <script>
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
    </div>
</x-app-layout>