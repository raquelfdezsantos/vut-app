<x-guest-layout>
    <div class="text-center py-20">
        <h1 class="text-4xl font-bold text-red-600 mb-4">403 | Acceso denegado</h1>
        <p class="text-gray-600 mb-6">No tienes permiso para acceder a esta página.</p>
        <a href="{{ url()->previous() }}" class="text-indigo-600 hover:underline">Volver atrás</a>
    </div>
</x-guest-layout>
