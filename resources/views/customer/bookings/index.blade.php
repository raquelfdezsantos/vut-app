<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            Mis reservas
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mt-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mt-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        @if($reservations->isEmpty())
            <div class="bg-white p-6 rounded shadow mt-4">
                <p>No tienes reservas aún.</p>
                <a href="{{ url('/') }}" class="text-indigo-600 underline">Ir a la propiedad</a>
            </div>
        @else
            <div class="bg-white p-6 rounded shadow mt-4 overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-left border-b">
                            <th class="py-2 px-3">Alojamiento</th>
                            <th class="py-2 px-3">Check-in</th>
                            <th class="py-2 px-3">Check-out</th>
                            <th class="py-2 px-3">Huéspedes</th>
                            <th class="py-2 px-3">Total</th>
                            <th class="py-2 px-3">Estado</th>
                            <th class="py-2 px-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $r)
                            <tr class="border-b">
                                <td class="py-2 px-3">{{ $r->property->name ?? '—' }}</td>
                                <td class="py-2 px-3">{{ $r->check_in->format('d/m/Y') }}</td>
                                <td class="py-2 px-3">{{ $r->check_out->format('d/m/Y') }}</td>
                                <td class="py-2 px-3">{{ $r->guests }}</td>
                                <td class="py-2 px-3">{{ number_format($r->total_price, 2, ',', '.') }} €</td>
                                <td class="py-2 px-3">{{ ucfirst($r->status) }}</td>
                                <td class="py-2 px-3">
                                    @if($r->status === 'pending')
                                        <form method="POST" action="{{ route('reservations.pay', $r->id) }}" class="inline">
                                            @csrf
                                            <button
                                                class="inline-block px-3 py-1 rounded bg-indigo-600 text-white text-sm hover:bg-indigo-700"
                                                onclick="return confirm('¿Simular pago de esta reserva?')">
                                                Pagar
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="py-2 space-x-3">
                                    @if($r->status !== 'cancelled')
                                        {{-- Editar siempre (pending o paid) --}}
                                        <a href="{{ route('reservas.edit', $r) }}"
                                            class="text-indigo-600 hover:underline">Editar</a>

                                        {{-- Cancelar siempre (pending o paid) --}}
                                        <form method="POST" action="{{ route('reservas.cancel', $r) }}" class="inline">
                                            @csrf
                                            <button class="text-red-600 hover:underline"
                                                onclick="return confirm('¿Cancelar esta reserva?')">
                                                Cancelar
                                            </button>
                                        </form>

                                        {{-- Ver factura si existe --}}
                                        @if($r->invoice)
                                            <a href="{{ route('invoices.show', $r->invoice->number) }}"
                                                class="text-indigo-600 hover:underline">Ver factura</a>
                                        @endif

                                        {{-- Pagar diferencia solo si la reserva está pagada y hay balance pendiente --}}
                                        @if($r->status === 'paid' && method_exists($r, 'balanceDue') && $r->balanceDue() > 0)
                                            <form method="POST" action="{{ route('reservations.pay_difference', $r->id) }}"
                                                class="inline">
                                                @csrf
                                                <button class="text-amber-600 hover:underline"
                                                    onclick="return confirm('Pagar diferencia de {{ number_format($r->balanceDue(), 2, ',', '.') }} €?')">
                                                    Pagar diferencia
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>

                <div class="mt-4">
                    {{ $reservations->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>