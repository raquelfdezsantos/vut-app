<div id="map" class="w-full h-[360px] rounded-2xl overflow-hidden border border-sn-accent/40"></div>

@pushOnce('scripts')
  {{-- script de Google Maps JS API + initMap --}}
  <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=marker" async defer></script>
  <script>
    window.initMap = function() {
      const pos = { lat: {{ $lat ?? 43.545 }}, lng: {{ $lng ?? -5.661 }} }; // valores por defecto Gijón si no pasas coords
      const map = new google.maps.Map(document.getElementById('map'), {
        zoom: {{ $zoom ?? 14 }}, center: pos, disableDefaultUI: true
      });
      new google.maps.marker.AdvancedMarkerElement({ map, position: pos });
    };
    // Si la API no llama a initCallback automáticamente, dispara manual tras load:
    window.addEventListener('load', () => setTimeout(() => window.initMap && window.initMap(), 500));
  </script>
@endPushOnce
