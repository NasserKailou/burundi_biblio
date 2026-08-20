<?php

namespace Database\Seeders;

use App\Models\Parametre;
use Illuminate\Database\Seeder;

class ParametreSeeder extends Seeder
{
    public function run(): void
    {
        $parametres = [
            'validation_auto' => 'false',
            'etablissement_nom' => 'Etablissement scolaire',
            'etablissement_logo' => '',
            'taille_max_fichier_mo' => '100',
            'formats_autorises' => 'pdf,epub',
            'politique_mdp_longueur_min' => '8',
            'duree_conservation_consultations_jours' => '730',
        ];

        foreach ($parametres as $cle => $valeur) {
            Parametre::query()->updateOrCreate(['cle' => $cle], ['valeur' => $valeur]);
        }
    }
}
