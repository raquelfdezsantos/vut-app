<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-lg text-gray-800 leading-tight">Pago cancelado</h2>
  </x-slot>

  <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded text-center">
    <h3 class="text-2xl font-semibold mb-4 text-red-600">Pago cancelado</h3>
    <p>Tu reserva no ha sido pagada. Puedes intentarlo de nuevo cuando quieras.</p>
    <a href="{{ route('reservas.index') }}" class="mt-6 inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Volver a mis reservas</a>
  </div>
</x-app-layout>
