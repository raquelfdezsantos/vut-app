<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Photo;
use App\Models\RateCalendar;
use Carbon\Carbon;

class MultiPropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * ⚠️ IMPORTANTE: Este seeder es SOLO para DEMOSTRACIÓN de escalabilidad (RNF9).
     * 
     * Crea 3 propiedades adicionales con datos realistas:
     * - 2 propiedades activas con fotos y calendario completo
     * - 1 propiedad soft-deleted (dada de baja hace 15 días)
     * 
     * USO:
     * - Para DEMO/DEFENSA: Descomentar en DatabaseSeeder.php
     * - En PRODUCCIÓN: Las propiedades se crean manualmente desde el panel admin
     * 
     * Demuestra:
     * - Sistema multi-propiedad funcional
     * - Gestión de soft deletes
     * - Calendario con tarifas dinámicas (temporada alta, fines de semana, festivos)
     * - Sistema de fotos con múltiples imágenes por propiedad
     */
    public function run(): void
    {
        // 1. Apartamento Centro (ACTIVA)
        $centro = Property::create([
            'name' => 'Apartamento Turístico Centro',
            'slug' => 'apartamento-turistico-centro',
            'description' => 'Acogedor apartamento en pleno centro histórico de Valencia. Totalmente reformado, con aire acondicionado, WiFi de alta velocidad y vistas a la calle peatonal. A 5 minutos andando de la Catedral y el Mercado Central. Ideal para parejas o familias pequeñas que quieran disfrutar de la ciudad sin necesidad de coche.',
            'address' => 'Calle San Vicente Mártir, 28',
            'city' => 'Valencia',
            'capacity' => 4,
        ]);

        // Fotos apartamento centro
        Photo::create(['property_id' => $centro->id, 'url' => 'https://picsum.photos/seed/centro-salon/1200/800', 'is_cover' => true, 'sort_order' => 1]);
        Photo::create(['property_id' => $centro->id, 'url' => 'https://picsum.photos/seed/centro-cocina/1200/800', 'is_cover' => false, 'sort_order' => 2]);
        Photo::create(['property_id' => $centro->id, 'url' => 'https://picsum.photos/seed/centro-dormitorio/1200/800', 'is_cover' => false, 'sort_order' => 3]);
        Photo::create(['property_id' => $centro->id, 'url' => 'https://picsum.photos/seed/centro-bano/1200/800', 'is_cover' => false, 'sort_order' => 4]);

        // Calendario de tarifas (próximos 90 días)
        $this->generateRateCalendar($centro->id, basePrice: 85, weekendPrice: 110);

        // 2. Chalet con Piscina (ACTIVA)
        $chalet = Property::create([
            'name' => 'Chalet con Piscina y Jardín',
            'slug' => 'chalet-piscina-jardin',
            'description' => 'Espectacular chalet independiente con piscina privada, jardín de 200m² y barbacoa. Ubicado en zona residencial tranquila a 15 minutos del centro. Dispone de 3 dormitorios dobles, 2 baños completos, salón amplio con chimenea y garaje para 2 coches. Perfecto para grupos y familias que buscan privacidad y comodidad.',
            'address' => 'Urbanización Las Palmeras, Parcela 42',
            'city' => 'Valencia',
            'capacity' => 8,
        ]);

        // Fotos chalet
        Photo::create(['property_id' => $chalet->id, 'url' => 'https://picsum.photos/seed/chalet-exterior/1200/800', 'is_cover' => true, 'sort_order' => 1]);
        Photo::create(['property_id' => $chalet->id, 'url' => 'https://picsum.photos/seed/chalet-piscina/1200/800', 'is_cover' => false, 'sort_order' => 2]);
        Photo::create(['property_id' => $chalet->id, 'url' => 'https://picsum.photos/seed/chalet-salon/1200/800', 'is_cover' => false, 'sort_order' => 3]);
        Photo::create(['property_id' => $chalet->id, 'url' => 'https://picsum.photos/seed/chalet-cocina/1200/800', 'is_cover' => false, 'sort_order' => 4]);
        Photo::create(['property_id' => $chalet->id, 'url' => 'https://picsum.photos/seed/chalet-dormitorio1/1200/800', 'is_cover' => false, 'sort_order' => 5]);

        // Calendario de tarifas (precio más alto por ser chalet premium)
        $this->generateRateCalendar($chalet->id, basePrice: 150, weekendPrice: 200);

        // 3. Estudio Playa (SOFT DELETED - dada de baja)
        $estudio = Property::create([
            'name' => 'Estudio Primera Línea de Playa',
            'slug' => 'estudio-playa-malvarrosa',
            'description' => 'Estudio moderno con vistas al mar en primera línea de playa de la Malvarrosa. Totalmente equipado con cocina americana, terraza con vistas panorámicas y parking incluido. A pie de playa y cerca de restaurantes y transporte público.',
            'address' => 'Paseo Marítimo de la Malvarrosa, 155',
            'city' => 'Valencia',
            'capacity' => 2,
            'deleted_at' => now()->subDays(15), // Soft delete hace 15 días
        ]);

        // Fotos estudio (aunque esté borrado, las fotos quedan)
        Photo::create(['property_id' => $estudio->id, 'url' => 'https://picsum.photos/seed/estudio-vista/1200/800', 'is_cover' => true, 'sort_order' => 1]);
        Photo::create(['property_id' => $estudio->id, 'url' => 'https://picsum.photos/seed/estudio-terraza/1200/800', 'is_cover' => false, 'sort_order' => 2]);
        Photo::create(['property_id' => $estudio->id, 'url' => 'https://picsum.photos/seed/estudio-interior/1200/800', 'is_cover' => false, 'sort_order' => 3]);

        // Calendario de tarifas (no tiene futuro porque está dado de baja, pero tiene histórico)
        $this->generateRateCalendar($estudio->id, basePrice: 70, weekendPrice: 95, onlyPast: true);
    }

    /**
     * Genera calendario de tarifas para una propiedad
     */
    private function generateRateCalendar(int $propertyId, int $basePrice, int $weekendPrice, bool $onlyPast = false): void
    {
        $startDate = $onlyPast ? now()->subDays(60) : now();
        $endDate = $onlyPast ? now()->subDays(1) : now()->addDays(90);

        for ($date = Carbon::parse($startDate); $date->lte($endDate); $date->addDay()) {
            // Fin de semana (viernes y sábado) son más caros
            $isWeekend = in_array($date->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY]);
            $price = $isWeekend ? $weekendPrice : $basePrice;

            // Temporada alta (julio-agosto): +20%
            $isHighSeason = in_array($date->month, [7, 8]);
            if ($isHighSeason) {
                $price = (int) ($price * 1.2);
            }

            // Navidad/Fin de año (20 dic - 6 ene): +30%
            $isChristmas = ($date->month === 12 && $date->day >= 20) || ($date->month === 1 && $date->day <= 6);
            if ($isChristmas) {
                $price = (int) ($price * 1.3);
            }

            // Fallas (15-19 marzo): +40%
            $isFallas = $date->month === 3 && $date->day >= 15 && $date->day <= 19;
            if ($isFallas) {
                $price = (int) ($price * 1.4);
            }

            // 20% de las fechas están bloqueadas aleatoriamente (mantenimiento, limpieza, etc.)
            $isBlocked = !$onlyPast && rand(1, 100) <= 20;

            RateCalendar::create([
                'property_id' => $propertyId,
                'date' => $date->toDateString(),
                'price' => $price,
                'is_available' => !$isBlocked,
                'min_stay' => $isWeekend || $isHighSeason || $isChristmas || $isFallas ? 2 : 1,
                'blocked_by' => $isBlocked ? 'admin' : null,
            ]);
        }
    }
}
