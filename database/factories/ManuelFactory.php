<?php

namespace Database\Factories;

use App\Models\Manuel;
use App\Models\Matiere;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Manuel>
 */
class ManuelFactory extends Factory
{
    protected $model = Manuel::class;

    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'auteur' => fake()->name(),
            'annee' => fake()->numberBetween(2015, 2026),
            'matiere_id' => Matiere::factory(),
            'fichier' => Str::uuid()->toString().'.pdf',
            'couverture' => Str::uuid()->toString().'.jpg',
            'type' => Manuel::TYPE_PDF,
            'est_commun' => false,
            'uploader_id' => User::factory()->enseignant(),
            'statut' => Manuel::STATUT_PUBLIE,
        ];
    }

    public function brouillon(): static
    {
        return $this->state(fn () => ['statut' => Manuel::STATUT_BROUILLON]);
    }

    public function commun(): static
    {
        return $this->state(fn () => ['est_commun' => true]);
    }
}
