<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis reservas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                @if($reservations->count())
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Propiedad</th>
                                <th class="py-2">Entrada</th>
                                <th class="py-2">Salida</th>
                                <th class="py-2">Huéspedes</th>
                                <th class="py-2">Estado</th>
                                <th class="py-2 text-right">Total (€)</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($reservations as $r)
                            <tr class="border-b">
                                <td class="py-2">{{ $r->property->name }}</td>
                                <td class="py-2">{{ \Carbon\Carbon::parse($r->check_in)->format('d/m/Y') }}</td>
                                <td class="py-2">{{ \Carbon\Carbon::parse($r->check_out)->format('d/m/Y') }}</td>
                                <td class="py-2">{{ $r->guests }}</td>
                                <td class="py-2 capitalize">{{ $r->status }}</td>
                                <td class="py-2 text-right">{{ number_format($r->total_price, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $reservations->links() }}
                    </div>
                @else
                    <p>No tienes reservas todavía.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
