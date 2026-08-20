<?php

namespace Database\Seeders;

use App\Models\Manuel;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ManuelSeeder extends Seeder
{
    public function run(): void
    {
        $assetsDir = database_path('seeders/assets');

        $catalogue = [
            ['slug' => 'mathematiques-6e', 'titre' => 'Mathematiques 6e', 'matiere' => 'Mathematiques', 'type' => 'pdf', 'niveaux' => ['6eme'], 'commun' => false, 'uploader' => 'enseignant1', 'auteur' => 'Collectif BNS', 'annee' => 2024],
            ['slug' => 'francais-6e', 'titre' => 'Francais 6e - Grammaire et Conjugaison', 'matiere' => 'Francais', 'type' => 'pdf', 'niveaux' => ['6eme'], 'commun' => false, 'uploader' => 'enseignant1', 'auteur' => 'Collectif BNS', 'annee' => 2024],
            ['slug' => 'histoire-geo-5e', 'titre' => 'Histoire-Geographie 5e', 'matiere' => 'Histoire-Geographie', 'type' => 'epub', 'niveaux' => ['5eme'], 'commun' => false, 'uploader' => 'enseignant2', 'auteur' => 'Collectif BNS', 'annee' => 2023],
            ['slug' => 'svt-4e', 'titre' => 'Sciences de la Vie et de la Terre 4e', 'matiere' => 'SVT', 'type' => 'pdf', 'niveaux' => ['4eme'], 'commun' => false, 'uploader' => 'enseignant2', 'auteur' => 'Collectif BNS', 'annee' => 2023],
            ['slug' => 'physique-chimie-3e', 'titre' => 'Physique-Chimie 3e', 'matiere' => 'Physique-Chimie', 'type' => 'pdf', 'niveaux' => ['3eme'], 'commun' => false, 'uploader' => 'enseignant3', 'auteur' => 'Collectif BNS', 'annee' => 2024],
            ['slug' => 'anglais-2nde', 'titre' => 'Anglais 2nde', 'matiere' => 'Anglais', 'type' => 'epub', 'niveaux' => ['2nde'], 'commun' => false, 'uploader' => 'enseignant1', 'auteur' => 'Collectif BNS', 'annee' => 2022],
            ['slug' => 'mathematiques-terminale', 'titre' => 'Mathematiques Terminale', 'matiere' => 'Mathematiques', 'type' => 'pdf', 'niveaux' => ['Terminale'], 'commun' => false, 'uploader' => 'enseignant3', 'auteur' => 'Collectif BNS', 'annee' => 2024],
            ['slug' => 'dictionnaire-francais', 'titre' => 'Dictionnaire Francais (ressource commune)', 'matiere' => 'Francais', 'type' => 'pdf', 'niveaux' => [], 'commun' => true, 'uploader' => 'enseignant1', 'auteur' => 'Collectif BNS', 'annee' => 2021],
            ['slug' => 'atlas-histoire-geo', 'titre' => 'Atlas Histoire-Geographie (ressource commune)', 'matiere' => 'Histoire-Geographie', 'type' => 'epub', 'niveaux' => [], 'commun' => true, 'uploader' => 'enseignant2', 'auteur' => 'Collectif BNS', 'annee' => 2022],
            ['slug' => 'guide-methodologie', 'titre' => 'Guide Methodologique - Reussir ses etudes (ressource commune)', 'matiere' => 'Education Civique', 'type' => 'pdf', 'niveaux' => [], 'commun' => true, 'uploader' => 'enseignant3', 'auteur' => 'Collectif BNS', 'annee' => 2025],
        ];

        foreach ($catalogue as $m) {
            $this->creerManuel($assetsDir, $m, Manuel::STATUT_PUBLIE);
        }

        // Un manuel en brouillon (non visible des eleves) pour demontrer le workflow de publication.
        $this->creerManuel($assetsDir, [
            'slug' => 'mathematiques-terminale',
            'titre' => 'Mathematiques Terminale - Chapitre complementaire (en preparation)',
            'matiere' => 'Mathematiques',
            'type' => 'pdf',
            'niveaux' => ['Terminale'],
            'commun' => false,
            'uploader' => 'enseignant3',
            'auteur' => 'Collectif BNS',
            'annee' => 2026,
        ], Manuel::STATUT_BROUILLON);
    }

    private function creerManuel(string $assetsDir, array $m, string $statut): void
    {
        $uploader = User::query()->where('identifiant', $m['uploader'])->firstOrFail();
        $matiere = Matiere::query()->where('libelle', $m['matiere'])->firstOrFail();

        $ext = $m['type'];
        $sourceFichier = "{$assetsDir}/manuels/{$m['slug']}.{$ext}";
        $sourceCouverture = "{$assetsDir}/couvertures/{$m['slug']}.jpg";

        $nomFichier = Str::uuid()->toString().'.'.$ext;
        $nomCouverture = Str::uuid()->toString().'.jpg';

        Storage::disk('manuels')->put($nomFichier, file_get_contents($sourceFichier));
        Storage::disk('couvertures')->put($nomCouverture, file_get_contents($sourceCouverture));

        $manuel = Manuel::query()->create([
            'titre' => $m['titre'],
            'description' => "Manuel de demonstration : {$m['titre']}.",
            'auteur' => $m['auteur'],
            'annee' => $m['annee'],
            'matiere_id' => $matiere->id,
            'fichier' => $nomFichier,
            'couverture' => $nomCouverture,
            'type' => $m['type'],
            'est_commun' => $m['commun'],
            'uploader_id' => $uploader->id,
            'statut' => $statut,
        ]);

        if (! empty($m['niveaux'])) {
            $niveauIds = Niveau::query()->whereIn('libelle', $m['niveaux'])->pluck('id');
            $manuel->niveaux()->sync($niveauIds);
        }
    }
}
