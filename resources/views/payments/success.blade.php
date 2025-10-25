<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-lg text-gray-800 leading-tight">Pago completado</h2>
  </x-slot>

  <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded text-center">
    <h3 class="text-2xl font-semibold mb-4 text-green-600">¡Gracias por tu pago!</h3>
    <p>Tu reserva para <strong>{{ $reservation->property->name }}</strong> está confirmada.</p>
    <a href="{{ route('reservas.index') }}" class="mt-6 inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Ver mis reservas</a>
  </div>
</x-app-layout>
