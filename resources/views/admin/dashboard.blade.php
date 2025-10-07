<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Administración') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="mb-4">
                        Bienvenida, {{ auth()->user()->name }}. <br>
                        Aquí puedes gestionar propiedad, fotos, calendario y reservas.
                    </p>

                    <table class="w-full text-left border border-gray-200 rounded overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-3">ID</th>
                                <th class="p-3">Cliente</th>
                                <th class="p-3">Propiedad</th>
                                <th class="p-3">Check-in</th>
                                <th class="p-3">Check-out</th>
                                <th class="p-3">Estado</th>
                                <th class="p-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $r)
                                <tr class="border-t">
                                    <td class="p-3">{{ $r->id }}</td>
                                    <td class="p-3">{{ $r->user?->name ?? '—' }}</td>
                                    <td class="p-3">{{ $r->property?->name ?? '—' }}</td>
                                    <td class="p-3">{{ $r->check_in->format('Y-m-d') }}</td>
                                    <td class="p-3">{{ $r->check_out->format('Y-m-d') }}</td>
                                    <td class="p-3">{{ ucfirst($r->status) }}</td>
                                    <td class="p-3">
                                        @if($r->status === 'pending')
                                            <form method="POST" action="{{ route('admin.reservations.cancel', $r->id) }}">
                                                @csrf
                                                <button class="px-3 py-1 rounded bg-red-600 text-white text-sm"
                                                    onclick="return confirm('¿Cancelar esta reserva y reponer noches?')">
                                                    Cancelar
                                                </button>
                                            </form>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="p-3" colspan="7">No hay reservas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $reservations->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
