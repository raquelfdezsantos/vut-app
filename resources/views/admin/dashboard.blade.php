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

            {{-- Widgets de estadísticas --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                {{-- Reservas activas --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Reservas Activas</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ $stats['activeReservations'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ingresos totales --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Ingresos Totales</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['totalRevenue'], 2, ',', '.') }} €</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ocupación del mes --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Ocupación {{ now()->format('F') }}</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ $stats['occupancyRate'] }}%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Próximas reservas --}}
            @if($stats['upcomingReservations']->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Próximas Reservas</h3>
                        <div class="space-y-3">
                            @foreach($stats['upcomingReservations'] as $upcoming)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-indigo-600 font-semibold">{{ substr($upcoming->user->name ?? 'U', 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $upcoming->user->name ?? 'Usuario' }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $upcoming->check_in->format('d/m/Y') }} - {{ $upcoming->check_out->format('d/m/Y') }}
                                                · {{ $upcoming->guests }} huésped(es)
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-900">{{ number_format($upcoming->total_price, 2, ',', '.') }} €</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $upcoming->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($upcoming->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Todas las Reservas</h3>

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