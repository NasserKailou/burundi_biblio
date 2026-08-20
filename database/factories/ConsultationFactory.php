<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\Manuel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consultation>
 */
class ConsultationFactory extends Factory
{
    protected $model = Consultation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->eleve(),
            'manuel_id' => Manuel::factory(),
            'date_ouverture' => fake()->dateTimeBetween('-60 days', 'now'),
            'duree_secondes' => fake()->numberBetween(60, 2000),
            'derniere_page' => fake()->numberBetween(1, 40),
        ];
    }
}
