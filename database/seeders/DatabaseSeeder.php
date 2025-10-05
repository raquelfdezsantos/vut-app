<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder principal de la base de datos.
 *
 * Ejecuta los seeders necesarios para inicializar el entorno de desarrollo
 * con usuarios, propiedades, fotos y calendario de tarifas.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta los seeders definidos en la aplicación.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(InitialDataSeeder::class);
    }
}
