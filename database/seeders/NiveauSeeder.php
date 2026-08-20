<?php

namespace Database\Seeders;

use App\Models\Niveau;
use Illuminate\Database\Seeder;

class NiveauSeeder extends Seeder
{
    public function run(): void
    {
        $niveaux = [
            ['libelle' => '6eme', 'ordre' => 1],
            ['libelle' => '5eme', 'ordre' => 2],
            ['libelle' => '4eme', 'ordre' => 3],
            ['libelle' => '3eme', 'ordre' => 4],
            ['libelle' => '2nde', 'ordre' => 5],
            ['libelle' => '1ere', 'ordre' => 6],
            ['libelle' => 'Terminale', 'ordre' => 7],
        ];

        foreach ($niveaux as $niveau) {
            Niveau::query()->updateOrCreate(['libelle' => $niveau['libelle']], $niveau);
        }
    }
}
