<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database avec les donnees de demo BNS.
     * Ordre important : referentiels -> utilisateurs -> catalogue -> activite.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            NiveauSeeder::class,
            MatiereSeeder::class,
            ParametreSeeder::class,
            UserSeeder::class,
            ManuelSeeder::class,
            ConsultationSeeder::class,
        ]);
    }
}
