<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Administración') }}
        </h2>
    </x-slot>

    <div class="mb-4 flex gap-2">
        <a href="{{ route('admin.property.index') }}" class="px-3 py-1 rounded bg-gray-100 border text-sm">Propiedad</a>
        <a href="{{ route('admin.photos.index') }}" class="px-3 py-1 rounded bg-gray-100 border text-sm">Fotos</a>
        <a href="{{ route('admin.calendar.index') }}"
            class="px-3 py-1 rounded bg-gray-100 border text-sm">Calendario</a>
    </div>

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
                                <th class="p-3">Huéspedes</th>
                                <th class="p-3">Total</th>
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
                                    <td class="p-3">{{ $r->guests }}</td>
                                    <td class="p-3">{{ number_format($r->total_price, 2, ',', '.') }} €</td>
                                    <td class="p-3">{{ ucfirst($r->status) }}</td>
                                    <td class="p-3">
                                        {{-- Editar siempre en admin --}}
                                        <a href="{{ route('admin.reservations.edit', $r->id) }}"
                                            class="text-indigo-600 hover:underline">Editar</a>

                                        @if ($r->status === 'pending')
                                            <div class="flex gap-2">
                                                <form method="POST" action="{{ route('reservations.pay', $r->id) }}">
                                                    @csrf
                                                    <button class="px-3 py-1 rounded bg-indigo-600 text-white text-sm"
                                                        onclick="return confirm('¿Marcar como pagada y generar factura?')">
                                                        Marcar pagada
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.reservations.cancel', $r->id) }}">
                                                    @csrf
                                                    <button class="px-3 py-1 rounded bg-red-600 text-white text-sm"
                                                        onclick="return confirm('¿Cancelar esta reserva y reponer noches?')">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            </div>

                                        @elseif ($r->status === 'paid' && $r->invoice)
                                            <div class="flex gap-2 items-center">
                                                <a class="text-indigo-600 hover:underline"
                                                    href="{{ route('invoices.show', $r->invoice->number) }}">
                                                    Ver factura
                                                </a>
                                                <a class="text-indigo-600 hover:underline ml-3"
                                                    href="{{ route('invoices.show', $r->invoice->number) }}?download=1">
                                                    Descargar PDF
                                                </a>

                                                <form method="POST" action="{{ route('admin.reservations.refund', $r->id) }}"
                                                    class="inline">
                                                    @csrf
                                                    <button
                                                        class="text-red-600 hover:underline"
                                                        onclick="return confirm('Esto marcará la reserva como cancelada y registrará reembolso. ¿Continuar?')">
                                                        Reembolsar y cancelar
                                                    </button>
                                                </form>
                                            </div>


                                        @elseif ($r->status === 'paid')
                                            {{-- Pagada pero aún sin invoice (raro, pero por si acaso) --}}
                                            <span class="text-gray-500">Sin factura</span>

                                        @else
                                            —
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td class="p-3" colspan="9">No hay reservas.</td>
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