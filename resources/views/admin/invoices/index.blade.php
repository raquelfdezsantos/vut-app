<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">Mis facturas</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow mt-4 overflow-x-auto">
            <table class="min-w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="py-2">Nº</th>
                        <th class="py-2">Emitida</th>
                        <th class="py-2">Alojamiento</th>
                        <th class="py-2">Fechas</th>
                        <th class="py-2">Importe</th>
                        <th class="py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr class="border-b">
                            <td class="py-2">{{ $inv->number }}</td>
                            <td class="py-2">{{ optional($inv->issued_at)->format('d/m/Y') }}</td>
                            <td class="py-2">{{ $inv->reservation->property->name ?? '—' }}</td>
                            <td class="py-2">
                                {{ $inv->reservation->check_in->format('d/m/Y') }} →
                                {{ $inv->reservation->check_out->format('d/m/Y') }}
                            </td>
                            <td class="py-2">{{ number_format($inv->amount, 2, ',', '.') }} €</td>
                            <td class="py-2 space-x-2">
                            <td class="py-2 space-x-2">
                                <a class="text-indigo-600 hover:underline"
                                    href="{{ route('invoices.show', $inv->number) }}">Ver</a>
                                <a class="text-indigo-600 hover:underline ml-3"
                                    href="{{ route('invoices.show', $inv->number) }}?download=1">
                                    Descargar PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4">No tienes facturas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</x-app-layout>