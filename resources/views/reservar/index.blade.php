@extends('layouts.app')

@section('title', 'Reservar')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Calendario Flatpickr - Modo Oscuro */
        html[data-theme="dark"] .flatpickr-calendar {
            background: var(--color-bg-card);
            border: 1px solid var(--color-border-light);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        html[data-theme="dark"] .flatpickr-months {
            background: var(--color-bg-card);
            border-bottom: 1px solid var(--color-border-light);
        }
        
        html[data-theme="dark"] .flatpickr-current-month .flatpickr-monthDropdown-months,
        html[data-theme="dark"] .flatpickr-current-month input.cur-year {
            color: var(--color-text-primary);
            background: var(--color-bg-secondary);
        }
        
        html[data-theme="dark"] .flatpickr-weekdays {
            background: var(--color-bg-card);
        }
        
        html[data-theme="dark"] span.flatpickr-weekday {
            color: var(--color-text-secondary);
        }
        
        html[data-theme="dark"] .flatpickr-day {
            color: var(--color-text-primary);
        }
        
        html[data-theme="dark"] .flatpickr-day:hover:not(.flatpickr-disabled) {
            background: rgba(77, 141, 148, 0.10);
            border-color: var(--color-accent);
        }
        
        html[data-theme="dark"] .flatpickr-day.selected,
        html[data-theme="dark"] .flatpickr-day.startRange,
        html[data-theme="dark"] .flatpickr-day.endRange {
            background: var(--color-accent);
            border-color: var(--color-accent);
            color: white;
        }
        
        html[data-theme="dark"] .flatpickr-day.today {
            border-color: var(--color-accent);
        }
        
        html[data-theme="dark"] .flatpickr-months .flatpickr-prev-month svg,
        html[data-theme="dark"] .flatpickr-months .flatpickr-next-month svg {
            fill: var(--color-text-primary);
        }
        
        /* Modo Claro */
        html[data-theme="light"] .flatpickr-calendar {
            background: #d1d1d1;
            border: 1px solid #e0e0e0;
        }
        
        html[data-theme="light"] .flatpickr-months {
            background: #d1d1d1;
        }
        
        html[data-theme="light"] .flatpickr-weekdays {
            background: #d1d1d1;
        }
        
        html[data-theme="light"] .flatpickr-day {
            color: #222;
        }
        
        html[data-theme="light"] .flatpickr-day:hover:not(.flatpickr-disabled) {
            background: rgba(77, 141, 148, 0.10);
            border-color: var(--color-accent);
        }
        
        html[data-theme="light"] .flatpickr-day.selected,
        html[data-theme="light"] .flatpickr-day.startRange,
        html[data-theme="light"] .flatpickr-day.endRange {
            background: var(--color-accent);
            border-color: var(--color-accent);
            color: white;
        }
        
        html[data-theme="light"] .flatpickr-day.today {
            border-color: var(--color-accent);
        }
        
        /* Ajustar el grid de días para el nuevo tamaño */
        .flatpickr-days {
            width: 308px !important;
        }
        
        .dayContainer {
            width: 308px !important;
            min-width: 308px !important;
            max-width: 308px !important;
            justify-content: center !important;
        }
        
        /* Hacer los círculos más pequeños con espacio entre ellos */
        .flatpickr-day {
            max-width: 38px !important;
            max-height: 38px !important;
            width: 38px !important;
            height: 38px !important;
            line-height: 38px !important;
            margin: 2px !important;
        }
        
        /* Días de meses anterior/posterior más apagados */
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            opacity: 0.4 !important;
        }
        
        /* Asegurar opacidad en días de otros meses incluso con colores */
        .flatpickr-day.prevMonthDay.available,
        .flatpickr-day.prevMonthDay.unavailable,
        .flatpickr-day.nextMonthDay.available,
        .flatpickr-day.nextMonthDay.unavailable {
            opacity: 0.4 !important;
        }
        
        /* Colores de disponibilidad - Días disponibles (verde) */
        .flatpickr-day.available:not(.flatpickr-disabled):not(.selected) {
            background: var(--color-success) !important;
            color: white !important;
            border-color: var(--color-success) !important;
        }
        
        /* Colores de disponibilidad - Días NO disponibles (rojo) */
        .flatpickr-day.unavailable,
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.unavailable.flatpickr-disabled {
            background: var(--color-error) !important;
            color: white !important;
            opacity: 0.7 !important;
            border-color: var(--color-error) !important;
            cursor: not-allowed !important;
        }
        
        /* Asegurar que el hover no cambie los colores base */
        .flatpickr-day.unavailable:hover,
        .flatpickr-day.flatpickr-disabled:hover {
            background: var(--color-error) !important;
            color: white !important;
        }
        
        .flatpickr-day.available:hover:not(.flatpickr-disabled):not(.selected) {
            background: var(--color-success) !important;
            color: white !important;
            filter: brightness(1.1);
        }
    </style>
    
    <div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-serif mb-3">Reservar</h1>
            <p class="text-neutral-300">Esta es una vista inicial para visualizar la estructura. Integraremos disponibilidad
                real y pasarela de pago más adelante.</p>
        </header>

        <!-- Mensaje de error -->
        <div id="error-message" class="alert alert-error mb-4 hidden">
            <strong>Revisa lo siguiente:</strong>
            <ul style="margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc;">
                <li id="error-text"></li>
            </ul>
        </div>

        <div class="grid md:grid-cols-3 gap-8" style="align-items: start;">
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

            <!-- Resumen - alineado con los inputs de fechas -->
            <aside class="space-y-4" style="margin-top: 3rem;">
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
                            dayElem.classList.add('unavailable');
                            dayElem.title = checkinDates.includes(dateStr)
                                ? 'Check-in programado - no disponible'
                                : 'Noche ocupada - no disponible';
                        } else {
                            dayElem.classList.add('available');
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
                            dayElem.classList.add('unavailable');
                        } else {
                            dayElem.classList.add('available');
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
                        showError('Por favor, selecciona las fechas de entrada y salida');
                        return;
                    }

                    // Ocultar error si todo está bien
                    hideError();

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

                // Funciones para mostrar/ocultar errores
                function showError(message) {
                    const errorDiv = document.getElementById('error-message');
                    const errorText = document.getElementById('error-text');
                    errorText.textContent = message;
                    errorDiv.classList.remove('hidden');
                    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }

                function hideError() {
                    const errorDiv = document.getElementById('error-message');
                    errorDiv.classList.add('hidden');
                }
            });
        </script>
    </div>
@endsection