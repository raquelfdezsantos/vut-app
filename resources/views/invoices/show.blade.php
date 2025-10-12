<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            Factura {{ $invoice->number }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            {{-- Cabecera --}}
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold">Factura {{ $invoice->number }}</h1>
                    <p class="text-sm text-gray-500">
                        Fecha de emisión: {{ $invoice->issued_at?->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-block text-xs font-medium tracking-wide bg-gray-100 text-gray-700 px-2 py-1 rounded">
                        @php $status = $invoice->reservation?->status ?? '—'; @endphp
                        Estado: {{ ucfirst($status) }}
                    </span>
                </div>
            </div>

            {{-- Datos principales --}}
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-sm font-medium text-gray-500 mb-1">Cliente</h2>
                    <p class="text-gray-800">
                        {{ $invoice->reservation?->user?->name ?? '—' }}<br>
                        <span class="text-gray-500 text-sm">{{ $invoice->reservation?->user?->email ?? '' }}</span>
                    </p>
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-500 mb-1">Alojamiento</h2>
                    <p class="text-gray-800">
                        {{ $invoice->reservation?->property?->name ?? '—' }}<br>
                        <span class="text-gray-500 text-sm">
                            Check-in: {{ optional($invoice->reservation?->check_in)->format('d/m/Y') ?? '—' }} ·
                            Check-out: {{ optional($invoice->reservation?->check_out)->format('d/m/Y') ?? '—' }}
                        </span>
                    </p>
                </div>
            </div>

            {{-- Concepto y tabla resumida (1 línea) --}}
            <div class="px-6">
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-gray-600">
                                <th class="px-4 py-2 font-medium">Concepto</th>
                                <th class="px-4 py-2 font-medium">Noches</th>
                                <th class="px-4 py-2 font-medium">Huéspedes</th>
                                <th class="px-4 py-2 font-medium text-right">Importe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $res = $invoice->reservation;
                                $nights = $res && $res->check_in && $res->check_out
                                    ? $res->check_in->diffInDays($res->check_out)
                                    : null;
                            @endphp
                            <tr class="border-t">
                                <td class="px-4 py-2">
                                    Reserva #{{ $res?->id ?? '—' }}
                                    @if($res?->property?->name)
                                        — {{ $res->property->name }}
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $nights ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $res?->guests ?? '—' }}</td>
                                <td class="px-4 py-2 text-right font-semibold">
                                    {{ number_format($invoice->amount ?? 0, 2, ',', '.') }} €
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td class="px-4 py-2 font-medium" colspan="3">Total</td>
                                <td class="px-4 py-2 text-right font-bold">
                                    {{ number_format($invoice->amount ?? 0, 2, ',', '.') }} €
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="px-6 py-4 flex items-center justify-between">
                <a href="{{ url()->previous() }}"
                   class="text-sm text-indigo-600 hover:text-indigo-700 underline">
                    ← Volver
                </a>

                {{-- Placeholder para luego generar PDF --}}
                @if($invoice->pdf_path)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($invoice->pdf_path) }}"
                       target="_blank"
                       class="inline-flex items-center text-sm bg-gray-800 text-white px-3 py-2 rounded hover:bg-gray-900">
                        Descargar PDF
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
