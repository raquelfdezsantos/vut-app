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

                                    @if($r->invoice)
                                        <a href="{{ route('invoices.show', $r->invoice->id) }}"
                                            class="inline-block px-3 py-1 rounded bg-gray-100 text-gray-800 text-sm border hover:bg-gray-200 ml-2">
                                            Ver factura
                                        </a>
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