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
        // Seeder principal: crea usuarios, la propiedad principal, fotos y calendario
        $this->call(InitialDataSeeder::class);
        
        // MultiPropertySeeder: Añade 3 propiedades adicionales para demostrar escalabilidad (RNF9)
        // Solo descomentar para DEMO o DEFENSA del proyecto. En producción no es necesario.
        // $this->call(MultiPropertySeeder::class);
    }
}
