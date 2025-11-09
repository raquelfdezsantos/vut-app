@extends('layouts.app')

@section('title', 'Reservar')

@section('content')
<div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
    <header class="mb-8 text-center">
        <h1 class="text-4xl font-serif mb-3">Reservar</h1>
        <p class="text-neutral-300">Esta es una vista inicial para visualizar la estructura. Integraremos disponibilidad real y pasarela de pago más adelante.</p>
    </header>

    <div class="grid md:grid-cols-3 gap-8">
        <!-- Formulario (placeholder) -->
        <form class="md:col-span-2 space-y-6" method="POST" action="#">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-neutral-300 mb-1">Fecha de entrada</label>
                    <input type="text" class="w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100" placeholder="DD/MM/AAAA" disabled>
                </div>
                <div>
                    <label class="block text-sm text-neutral-300 mb-1">Fecha de salida</label>
                    <input type="text" class="w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100" placeholder="DD/MM/AAAA" disabled>
                </div>
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-neutral-300 mb-1">Adultos</label>
                    <select class="w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100" disabled>
                        <option>1</option>
                        <option selected>2</option>
                        <option>3</option>
                        <option>4</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-neutral-300 mb-1">Niños</label>
                    <select class="w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100" disabled>
                        <option selected>0</option>
                        <option>1</option>
                        <option>2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-neutral-300 mb-1">Mascotas</label>
                    <select class="w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100" disabled>
                        <option selected>No</option>
                        <option>Sí</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm text-neutral-300 mb-1">Notas</label>
                <textarea class="w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 h-28" placeholder="Cuéntanos cualquier necesidad especial" disabled></textarea>
            </div>

            <div class="pt-2">
                <button type="button" class="bg-[color:var(--color-accent)] hover:bg-[color:var(--color-accent-hover)] text-white text-sm font-semibold px-5 py-2 rounded cursor-not-allowed opacity-60" disabled>
                    Buscar disponibilidad
                </button>
            </div>
        </form>

        <!-- Resumen -->
        <aside class="space-y-4">
            <div class="bg-neutral-800 border border-neutral-700 p-4" style="border-radius:var(--radius-base);">
                <h3 class="font-semibold mb-2">Resumen</h3>
                <ul class="text-sm text-neutral-300 space-y-1">
                    <li>Fechas: <span class="text-neutral-400">—</span></li>
                    <li>Huéspedes: <span class="text-neutral-400">2 adultos</span></li>
                    <li>Noches: <span class="text-neutral-400">—</span></li>
                </ul>
                <div class="mt-3 pt-3 border-t border-neutral-700">
                    <p class="text-sm">Total estimado</p>
                    <p class="text-2xl font-serif">—</p>
                </div>
            </div>
            <div class="text-xs text-neutral-400">
                La disponibilidad y precio se calcularán aquí. Después conectaremos con el flujo de reserva y pago existente.
            </div>
        </aside>
    </div>
</div>
@endsection