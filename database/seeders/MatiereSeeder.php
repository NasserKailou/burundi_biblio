<?php

namespace Database\Seeders;

use App\Models\Matiere;
use Illuminate\Database\Seeder;

class MatiereSeeder extends Seeder
{
    public function run(): void
    {
        $matieres = [
            'Mathematiques',
            'Francais',
            'Histoire-Geographie',
            'SVT',
            'Physique-Chimie',
            'Anglais',
            'Education Civique',
        ];

        foreach ($matieres as $libelle) {
            Matiere::query()->firstOrCreate(['libelle' => $libelle]);
        }
    }
}
