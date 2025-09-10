<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Property;
use App\Models\Photo;
use App\Models\RateCalendar;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Admin y cliente demo
        User::updateOrCreate(
            ['email' => 'admin@vut.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'), 
                'role' => 'admin',
                'phone' => '600000000',
            ]
        );

        User::updateOrCreate(
            ['email' => 'cliente@vut.test'],
            [
                'name' => 'Cliente Demo',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );

        // 2) Propiedad base
        $property = Property::updateOrCreate(
            ['slug' => 'piso-turistico-centro'],
            [
                'name' => 'Piso Turístico Centro',
                'description' => 'Alojamiento cómodo en el centro. Cerca de todo.',
                'address' => 'Calle Mayor 1',
                'city' => 'Madrid',
                'capacity' => 4, 
            ]
        );

        // 3) Fotos (usa URLs de prueba por ahora)
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

        // 4) Calendario de precios para los próximos 30 días
        $today = Carbon::today();
        for ($i = 0; $i < 30; $i++) {
            $date = $today->copy()->addDays($i);
            $isWeekend = $date->isWeekend();
            $price = $isWeekend ? 120.00 : 95.00; // ejemplo: finde más caro

            RateCalendar::updateOrCreate(
                ['property_id' => $property->id, 'date' => $date->toDateString()],
                ['price' => $price, 'is_available' => true, 'min_stay' => 2]
            );
        }
    }
}
