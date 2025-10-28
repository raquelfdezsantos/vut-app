<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Calendario (bloquear/desbloquear)</h2>
  </x-slot>

  <div class="max-w-3xl mx-auto p-6 bg-white rounded shadow mt-6 space-y-6">
    @if (session('success'))
      <div class="p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.calendar.block') }}" class="space-y-3">
      @csrf
      <h3 class="font-semibold">Bloquear noches</h3>
      <div class="bg-blue-50 border border-blue-200 rounded p-3 text-sm text-blue-800">
        <strong>Ejemplo:</strong> Para bloquear las noches del 14, 15 y 16 de noviembre:<br>
        → Desde: <code>2025-11-14</code> | Hasta: <code>2025-11-16</code><br>
        <span class="text-xs">Esto bloqueará esas 3 noches. Nadie podrá hacer check-in el 14, 15 o 16. El primer check-in disponible sería el 17.</span>
      </div>
      <div>
        <label class="block text-sm">Alojamiento</label>
        <select name="property_id" class="border rounded px-2 py-1 w-full">
          @foreach(\App\Models\Property::select('id','name')->get() as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
        @error('property_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
      </div>
      <div class="flex gap-3">
        <div class="flex-1">
          <label class="block text-sm">Desde (primera noche bloqueada)</label>
          <input type="date" name="start" class="border rounded px-2 py-1 w-full" required>
          @error('start') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>
        <div class="flex-1">
          <label class="block text-sm">Hasta (última noche bloqueada, INCLUSIVO)</label>
          <input type="date" name="end" class="border rounded px-2 py-1 w-full" required>
          @error('end') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>
      </div>
      <button class="bg-gray-200 text-gray-900 px-4 py-2 rounded">Bloquear</button>
    </form>

    <form method="POST" action="{{ route('admin.calendar.unblock') }}" class="space-y-3">
      @csrf
      <h3 class="font-semibold">Desbloquear noches</h3>
      <div>
        <label class="block text-sm">Alojamiento</label>
        <select name="property_id" class="border rounded px-2 py-1 w-full">
          @foreach(App\Models\Property::select('id','name')->get() as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="flex gap-3">
        <div class="flex-1">
          <label class="block text-sm">Desde (primera noche desbloqueada)</label>
          <input type="date" name="start" class="border rounded px-2 py-1 w-full" required>
        </div>
        <div class="flex-1">
          <label class="block text-sm">Hasta (última noche desbloqueada, INCLUSIVO)</label>
          <input type="date" name="end" class="border rounded px-2 py-1 w-full" required>
        </div>
      </div>
      <button class="bg-gray-200 text-gray-900 px-4 py-2 rounded">Desbloquear</button>
    </form>
  </div>
</x-app-layout>
