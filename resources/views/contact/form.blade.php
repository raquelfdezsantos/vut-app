
<x-app-layout :compactMain="true">
    <div class="w-full mx-auto p-6">
        <h1 style="font-family: var(--font-serif); font-weight:400; font-size: var(--text-2xl); margin-bottom: var(--spacing-lg);">Contacto</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Formulario a la izquierda -->
            <div class="p-6 rounded-lg" style="background: var(--color-bg-card); border: 1px solid var(--color-border-light);">
                @if (session('success'))
                    <div class="mb-4" style="padding: .6rem .75rem; background: #204b23; color:#c6f6d5; border:1px solid #2f6b33; border-radius:6px;">
                        {{ session('success') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" type="email" name="email" class="block mt-1 w-full" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="subject" value="Asunto" />
                        <x-text-input id="subject" name="subject" class="block mt-1 w-full" :value="old('subject')" required />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="message" value="Mensaje" />
                        <textarea id="message" name="message" rows="6" class="sn-input block mt-1 w-full rounded-md bg-neutral-800 border border-neutral-700 text-neutral-100 p-2" required>{{ old('message') }}</textarea>
                        <x-input-error :messages="$errors->get('message')" class="mt-2" />
                    </div>
                    <div>
                        <x-primary-button>Enviar</x-primary-button>
                    </div>
                </form>
            </div>
            <!-- Mapa y dirección a la derecha -->
            <div class="p-6 flex flex-col gap-4 rounded-lg" style="background: var(--color-bg-card); border: 1px solid var(--color-border-light);">
                <div>
                    <h2 style="font-size: var(--text-lg); font-weight:600; margin-bottom: .5rem;">Dónde estamos</h2>
                    <div style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom:.5rem;">
                        {{ optional($property)->address ?? 'Dirección no disponible' }}
                        @if(!empty(optional($property)->postal_code)) · {{ optional($property)->postal_code }}@endif
                        @if(!empty(optional($property)->city)) · {{ optional($property)->city }}@endif
                        @if(!empty(optional($property)->province)) ({{ optional($property)->province }})@endif
                    </div>
                </div>
                <div>
                    <div id="map" class="w-full" style="height: 420px; border:1px solid var(--color-border-light); border-radius:8px;"></div>
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
                            mapEl.innerHTML = '<p style="color: var(--color-text-muted); padding:.5rem;">Coordenadas no disponibles.</p>';
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