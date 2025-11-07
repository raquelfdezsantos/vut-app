@extends('layouts.app')

@section('title', 'Entorno')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <header class="mb-10 text-center">
        <h1 class="text-4xl font-serif mb-4">El Entorno</h1>
        <p class="text-neutral-300 max-w-2xl mx-auto">Descubre el contexto que rodea nuestras propiedades: naturaleza, cultura, actividades y servicios cercanos. Esta página mostrará módulos dinámicos más adelante.</p>
    </header>

    <!-- Sección destacada / hero -->
    <section class="mb-16 grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 aspect-video bg-neutral-800 rounded-lg flex items-center justify-center text-neutral-500">
            <span class="text-sm">[Mapa contextual / foto panorámica]</span>
        </div>
        <div class="space-y-4">
            <h2 class="text-xl font-semibold">Resumen</h2>
            <p class="text-neutral-300 text-sm leading-relaxed">Aquí irá un texto introductorio sobre el atractivo del área: clima, tranquilidad, accesos, puntos clave. Un copy persuasivo corto.</p>
            <ul class="text-sm text-neutral-400 list-disc pl-5 space-y-1">
                <li>Playas cercanas</li>
                <li>Rutas de senderismo</li>
                <li>Gastronomía local</li>
                <li>Servicios esenciales</li>
            </ul>
        </div>
    </section>

    <!-- Bloques temáticos -->
    <section class="space-y-16">
        <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-3">
                <h3 class="text-lg font-semibold">Naturaleza</h3>
                <p class="text-neutral-300 text-sm">Descripción breve de espacios naturales: bosques, montes, costa, reservas y biodiversidad que enriquecen la experiencia.</p>
                <div class="h-40 bg-neutral-800 rounded flex items-center justify-center text-neutral-500 text-xs">[Mini-galería / slider]</div>
            </div>
            <div class="space-y-3">
                <h3 class="text-lg font-semibold">Cultura y Patrimonio</h3>
                <p class="text-neutral-300 text-sm">Lugares históricos, arquitectura, festividades y tradiciones locales que aportan identidad al destino.</p>
                <div class="h-40 bg-neutral-800 rounded flex items-center justify-center text-neutral-500 text-xs">[Listado eventos / agenda]</div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-3">
                <h3 class="text-lg font-semibold">Actividades</h3>
                <p class="text-neutral-300 text-sm">Experiencias recomendadas: kayak, ciclismo, visitas guiadas, mercados, enoturismo.</p>
                <div class="h-40 bg-neutral-800 rounded flex items-center justify-center text-neutral-500 text-xs">[Cards actividades]</div>
            </div>
            <div class="space-y-3">
                <h3 class="text-lg font-semibold">Servicios Cercanos</h3>
                <p class="text-neutral-300 text-sm">Farmacia, supermercado, transporte público, centros médicos, alquileres y logística.</p>
                <div class="h-40 bg-neutral-800 rounded flex items-center justify-center text-neutral-500 text-xs">[Mapa POIs / list]</div>
            </div>
        </div>
    </section>

    <!-- CTA futura -->
    <section class="mt-20 text-center">
        <div class="inline-block bg-neutral-800 border border-neutral-700 rounded-lg px-8 py-6">
            <p class="text-neutral-200 mb-4">Pronto podrás explorar un mapa interactivo, ver eventos en tiempo real y planificar tu estancia de forma personalizada.</p>
            <a href="{{ route('reservar') }}" class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-5 py-2 rounded transition">Reservar ahora</a>
        </div>
    </section>
</div>
@endsection