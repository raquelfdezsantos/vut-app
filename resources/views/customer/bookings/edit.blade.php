<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-lg">Editar reserva</h2></x-slot>
  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <form method="POST" action="{{ route('reservas.update', $reservation) }}">
      @csrf @method('PUT')
      <div class="mb-3">
        <label class="block text-sm">Entrada</label>
        <input type="date" name="check_in" value="{{ old('check_in', $reservation->check_in->toDateString()) }}" class="border rounded w-full p-2">
        @error('check_in')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>
      <div class="mb-3">
        <label class="block text-sm">Salida</label>
        <input type="date" name="check_out" value="{{ old('check_out', $reservation->check_out->toDateString()) }}" class="border rounded w-full p-2">
        @error('check_out')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>
      <div class="mb-4">
        <label class="block text-sm">Huéspedes</label>
        <input type="number" min="1" name="guests" value="{{ old('guests', $reservation->guests) }}" class="border rounded w-full p-2">
        @error('guests')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>
      <button class="bg-indigo-600 text-white px-4 py-2 rounded">Guardar cambios</button>
    </form>
  </div>
</x-app-layout>
