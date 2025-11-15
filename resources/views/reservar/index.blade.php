@extends('layouts.app')

@section('title', 'Reservar')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-serif mb-3">Reservar</h1>
            <p class="text-neutral-300">Esta es una vista inicial para visualizar la estructura. Integraremos disponibilidad
                real y pasarela de pago más adelante.</p>
        </header>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Formulario (placeholder) -->
            <form class="md:col-span-2 space-y-6" method="GET" action="{{ route('login') }}" id="reservationForm">
                <input type="hidden" name="redirect" value="reservas.store">
                <input type="hidden" name="reservation_data" id="reservation_data">

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-neutral-300 mb-1">Fecha de entrada</label>
                        <input type="text" id="check_in"
                            class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                            placeholder="YYYY-MM-DD">
                    </div>
                    <div>
                        <label class="block text-sm text-neutral-300 mb-1">Fecha de salida</label>
                        <input type="text" id="check_out"
                            class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                            placeholder="YYYY-MM-DD">
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-neutral-300 mb-1">Adultos</label>
                        <select id="adults"
                            class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]">
                            <option value="1">1</option>
                            <option value="2" selected>2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-neutral-300 mb-1">Niños</label>
                        <select id="children"
                            class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]">
                            <option value="0" selected>0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-neutral-300 mb-1">Mascotas</label>
                        <select id="pets"
                            class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]">
                            <option value="0" selected>No</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-neutral-300 mb-1">Notas</label>
                    <textarea id="notes"
                        class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 h-28 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                        placeholder="Cuéntanos cualquier necesidad especial"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="bg-[color:var(--color-accent)] hover:bg-[color:var(--color-accent-hover)] text-white text-sm font-semibold px-5 py-2"
                        style="border-radius: 2px;">
                        Realizar reserva
                    </button>
                </div>
            </form>

            <!-- Resumen -->
            <aside class="space-y-4">
                <div class="bg-neutral-800 border border-neutral-700 p-4" style="border-radius:var(--radius-base);">
                    <h3 class="font-semibold mb-2">Resumen</h3>
                    <ul class="text-sm text-neutral-300 space-y-1">
                        <li>Fechas: <span class="text-neutral-400" id="summary-dates">—</span></li>
                        <li>Huéspedes: <span class="text-neutral-400" id="summary-guests">2 adultos</span></li>
                        <li>Noches: <span class="text-neutral-400" id="summary-nights">—</span></li>
                    </ul>
                    <div class="mt-3 pt-3 border-t border-neutral-700">
                        <p class="text-sm">Total estimado</p>
                        <p class="text-2xl font-serif" id="summary-total">—</p>
                    </div>
                </div>
                <div class="text-xs text-neutral-400">
                    La disponibilidad y precio se calcularán aquí. Después conectaremos con el flujo de reserva y pago
                    existente.
                </div>
            </aside>
        </div>

        {{-- Script Flatpickr --}}
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const blockedDates = @json($blockedDates ?? []);
                const checkinDates = @json($checkinDates ?? []);
                const fromPrice = {{ $fromPrice ?? 50 }};

                // Actualizar resumen de huéspedes
                function updateGuests() {
                    const adults = parseInt(document.getElementById('adults').value);
                    const children = parseInt(document.getElementById('children').value);

                    let text = adults + ' adulto' + (adults !== 1 ? 's' : '');
                    if (children > 0) {
                        text += ', ' + children + ' niño' + (children !== 1 ? 's' : '');
                    }
                    document.getElementById('summary-guests').textContent = text;
                    updateTotal();
                }

                document.getElementById('adults').addEventListener('change', updateGuests);
                document.getElementById('children').addEventListener('change', updateGuests);

                // Configurar Flatpickr para check-in
                const checkInPicker = flatpickr('#check_in', {
                    locale: 'es',
                    minDate: 'today',
                    dateFormat: 'Y-m-d',
                    disable: [
                        function (date) {
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            const dateStr = `${year}-${month}-${day}`;

                            if (checkinDates.includes(dateStr)) {
                                return true;
                            }

                            if (blockedDates.includes(dateStr)) {
                                return true;
                            }

                            return false;
                        }
                    ],
                    onChange: function (selectedDates) {
                        if (selectedDates.length) {
                            const nextDay = new Date(selectedDates[0].getTime());
                            nextDay.setDate(nextDay.getDate() + 1);
                            checkOutPicker.set('minDate', nextDay);
                            updateTotal();
                        }
                    },

                    onDayCreate: function (dObj, dStr, fp, dayElem) {
                        const date = dayElem.dateObj;
                        const y = date.getFullYear();
                        const m = String(date.getMonth() + 1).padStart(2, '0');
                        const d = String(date.getDate()).padStart(2, '0');
                        const dateStr = `${y}-${m}-${d}`;

                        if (checkinDates.includes(dateStr) || blockedDates.includes(dateStr)) {
                            dayElem.style.backgroundColor = '#ffebee';
                            dayElem.style.color = '#c62828';
                            dayElem.title = checkinDates.includes(dateStr)
                                ? 'Check-in programado - no disponible'
                                : 'Noche ocupada - no disponible';
                        } else {
                            dayElem.style.backgroundColor = '#e8f5e9';
                            dayElem.style.color = '#2e7d32';
                            dayElem.title = 'Disponible';
                        }
                    }

                });

                // Configurar Flatpickr para check-out
                // Calculamos mañana manualmente (sin usar fp_incr)
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);

                const checkOutPicker = flatpickr('#check_out', {
                    locale: 'es',
                    minDate: tomorrow,
                    dateFormat: 'Y-m-d',
                    disable: [
                        function (date) {
                            // Usamos el objeto Date de checkIn directamente
                            const checkInDate = checkInPicker.selectedDates[0];
                            if (!checkInDate) return false;

                            const checkOutDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                            const current = new Date(checkInDate.getTime());
                            current.setDate(current.getDate() + 1);

                            while (current < checkOutDate) {
                                const y = current.getFullYear();
                                const m = String(current.getMonth() + 1).padStart(2, '0');
                                const d = String(current.getDate()).padStart(2, '0');
                                const nightStr = `${y}-${m}-${d}`;

                                if (blockedDates.includes(nightStr)) {
                                    // Si alguna noche intermedia está bloqueada → no dejar seleccionar este checkout
                                    return true;
                                }
                                current.setDate(current.getDate() + 1);
                            }

                            return false;
                        }
                    ],
                    onChange: function () {
                        updateTotal();
                    },
                    onDayCreate: function (dObj, dStr, fp, dayElem) {
                        const date = dayElem.dateObj;
                        const y = date.getFullYear();
                        const m = String(date.getMonth() + 1).padStart(2, '0');
                        const d = String(date.getDate()).padStart(2, '0');
                        const dateStr = `${y}-${m}-${d}`;

                        if (blockedDates.includes(dateStr)) {
                            dayElem.style.backgroundColor = '#ffebee';
                            dayElem.style.color = '#c62828';
                        } else {
                            dayElem.style.backgroundColor = '#e8f5e9';
                        }
                    }
                });


                // Actualizar total
                function updateTotal() {
                    const checkIn = checkInPicker.selectedDates[0];
                    const checkOut = checkOutPicker.selectedDates[0];

                    if (checkIn && checkOut) {
                        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));

                        if (nights > 0) {
                            const formatDate = (d) => d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                            document.getElementById('summary-dates').textContent = `${formatDate(checkIn)} → ${formatDate(checkOut)}`;
                            document.getElementById('summary-nights').textContent = nights + ' noche' + (nights !== 1 ? 's' : '');

                            const total = nights * fromPrice;
                            document.getElementById('summary-total').textContent = total.toFixed(2) + '€';
                        }
                    }
                }

                // Guardar datos antes de enviar al login
                document.getElementById('reservationForm').addEventListener('submit', function (e) {
                    const checkIn = checkInPicker.selectedDates[0];
                    const checkOut = checkOutPicker.selectedDates[0];

                    if (!checkIn || !checkOut) {
                        e.preventDefault();
                        alert('Por favor, selecciona las fechas de entrada y salida');
                        return;
                    }

                    const data = {
                        property_id: {{ $property->id ?? 1 }},
                        check_in: checkInPicker.formatDate(checkIn, 'Y-m-d'),
                        check_out: checkOutPicker.formatDate(checkOut, 'Y-m-d'),
                        adults: document.getElementById('adults').value,
                        children: document.getElementById('children').value,
                        pets: document.getElementById('pets').value,
                        notes: document.getElementById('notes').value,
                        guests: parseInt(document.getElementById('adults').value) + parseInt(document.getElementById('children').value)
                    };

                    document.getElementById('reservation_data').value = JSON.stringify(data);
                    sessionStorage.setItem('pendingReservation', JSON.stringify(data));
                });
            });
        </script>
    </div>
@endsection