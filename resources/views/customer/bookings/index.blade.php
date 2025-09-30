<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            Mis reservas
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($reservations->isEmpty())
            <div class="bg-white p-6 rounded shadow mt-4">
                <p>No tienes reservas aún.</p>
                <a href="{{ url('/') }}" class="text-indigo-600 underline">Ir a la propiedad</a>
            </div>
        @else
            <div class="bg-white p-6 rounded shadow mt-4 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Alojamiento</th>
                        <th class="py-2">Entrada</th>
                        <th class="py-2">Salida</th>
                        <th class="py-2">Huéspedes</th>
                        <th class="py-2">Total</th>
                        <th class="py-2">Estado pago</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reservations as $r)
                        <tr class="border-b">
                            <<td class="py-2">{{ $r->property->name ?? $r->property->title }}</td>
                            <td class="py-2">{{ $r->check_in->format('d/m/Y') }}</td>
                            <td class="py-2">{{ $r->check_out->format('d/m/Y') }}</td>
                            <td class="py-2">{{ $r->guests }}</td>
                            <td class="py-2">{{ number_format($r->total_price, 2, ',', '.') }} €</td>
                            <td class="py-2">{{ $r->payment_status ?? 'pendiente' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
