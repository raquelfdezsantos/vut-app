<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Property;
use App\Models\Photo;
use App\Models\RateCalendar;

/**
 * Seeder de datos iniciales para entorno de desarrollo.
 *
 * Crea usuarios demo (admin y cliente), una propiedad base con fotos
 * y un calendario de tarifas para los próximos 30 días.
 */
class InitialDataSeeder extends Seeder
{
    /**
     * Ejecuta la inserción de datos base.
     *
     * @return void
     */
    public function run(): void
    {
        // Creación de administrador
        User::updateOrCreate(
            ['email' => 'admin@vut.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '600000000',
            ]
        );

        // Creación de usuario cliente
        User::updateOrCreate(
            ['email' => 'cliente@vut.test'],
            [
                'name' => 'Cliente Demo',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );

        // Propiedad de ejemplo
        $property = Property::updateOrCreate(
            ['slug' => 'piso-turistico-centro'],
            [
                'name' => 'Piso Turístico Centro',
                'description' => 'Alojamiento cómodo en el centro. Cerca de todo.',
                'address' => 'Avenida de Portugal, 18',
                'city' => 'Gijón',
                'postal_code' => '33207',
                'province' => 'Asturias',
                'capacity' => 4,
                'tourism_license' => 'VT-28-0001234',
                'rental_registration' => 'ATR-28-001234-2024',
            ]
        );

        // Fotos de ejemplo
        $photos = [
            ['url' => 'https://picsum.photos/seed/vut1/1200/800', 'is_cover' => true,  'sort_order' => 1],
            ['url' => 'https://picsum.photos/seed/vut2/1200/800', 'is_cover' => false, 'sort_order' => 2],
            ['url' => 'https://picsum.photos/seed/vut3/1200/800', 'is_cover' => false, 'sort_order' => 3],
        ];

        foreach ($photos as $p) {
            Photo::updateOrCreate(
                ['property_id' => $property->id, 'url' => $p['url']],
                ['is_cover' => $p['is_cover'], 'sort_order' => $p['sort_order']]
            );
        }

        // Calendario de precios (180 días)
        $today = Carbon::today();
        $days  = 180;

        for ($i = 0; $i < $days; $i++) {
            $date = $today->copy()->addDays($i);
            $isWeekend = $date->isWeekend();
            $price = $isWeekend ? 120.00 : 95.00;

            RateCalendar::updateOrCreate(
                ['property_id' => $property->id, 'date' => $date->toDateString()],
                ['price' => $price, 'is_available' => true, 'min_stay' => 2]
            );
        }
    }
}
