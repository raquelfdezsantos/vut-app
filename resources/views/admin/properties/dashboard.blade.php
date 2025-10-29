<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $property->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Navegación --}}
            <div class="mb-4 flex gap-2">
                <a href="{{ route('admin.properties.index') }}" class="px-3 py-1 rounded bg-gray-100 border text-sm hover:bg-gray-200">← Volver al listado</a>
                <a href="{{ route('admin.property.index', $property->id) }}" class="px-3 py-1 rounded bg-gray-100 border text-sm hover:bg-gray-200">Editar Propiedad</a>
                <a href="{{ route('admin.photos.index', $property->id) }}" class="px-3 py-1 rounded bg-gray-100 border text-sm hover:bg-gray-200">Gestión de Fotos</a>
                <a href="{{ route('admin.calendar.index') }}?property_id={{ $property->id }}" class="px-3 py-1 rounded bg-gray-100 border text-sm hover:bg-gray-200">Calendario</a>
            </div>

            {{-- Info de la propiedad --}}
            @if($property->trashed())
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                    <p class="font-semibold">⚠️ Esta propiedad está dada de baja</p>
                    <p class="text-sm mt-1">Fue eliminada el {{ $property->deleted_at->format('d/m/Y H:i') }}</p>
                </div>
            @endif

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            {{-- Reservas de esta propiedad --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Reservas de esta propiedad</h3>

                    @if($reservations->isEmpty())
                        <p class="text-gray-500 text-center py-8">No hay reservas para esta propiedad.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reservations as $reservation)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">#{{ $reservation->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->user->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->check_in->format('d/m/Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->check_out->format('d/m/Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $reservation->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $reservation->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ ucfirst($reservation->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($reservation->total_price, 2, ',', '.') }} €</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $reservations->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Zona de peligro: Dar de baja propiedad --}}
            @if(!$property->trashed())
                @php
                    $futureReservationsCount = \App\Models\Reservation::where('property_id', $property->id)
                        ->where('check_in', '>=', now())
                        ->whereIn('status', ['pending', 'paid'])
                        ->count();
                @endphp

                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-red-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-red-600 mb-4">⚠️ Zona de peligro</h3>
                        
                        <p class="text-sm text-gray-700 mb-4">
                            Una vez que des de baja la propiedad, se cancelarán todas las reservas futuras activas 
                            y se procesarán los reembolsos automáticamente.
                        </p>

                        @if($futureReservationsCount > 0)
                            <div class="mb-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700">
                                <p class="font-semibold">{{ $futureReservationsCount }} reserva(s) futura(s) será(n) cancelada(s)</p>
                                <p class="text-sm mt-1">Los clientes recibirán un reembolso completo y un email de notificación.</p>
                            </div>
                        @endif

                        <form 
                            method="POST" 
                            action="{{ route('admin.property.destroy', $property->id) }}"
                            onsubmit="return confirm('⚠️ ¿Estás seguro de que deseas dar de baja esta propiedad?\n\n' + 
                                ({{ $futureReservationsCount }} > 0 ? 'Se cancelarán {{ $futureReservationsCount }} reserva(s) futura(s) y se procesarán los reembolsos automáticamente.\n\n' : '') + 
                                'Esta acción NO es irreversible, podrás restaurarla después.')"
                        >
                            @csrf
                            @method('DELETE')

                            <button 
                                type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                Dar de baja propiedad
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
