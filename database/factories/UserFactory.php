<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $prenom = fake()->firstName();
        $nom = fake()->lastName();

        return [
            'nom' => $nom,
            'prenom' => $prenom,
            'identifiant' => Str::slug("{$prenom}.{$nom}").fake()->unique()->numberBetween(1, 9999),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role_id' => Role::query()->where('libelle', 'eleve')->value('id'),
            'niveau_id' => null,
            'classe' => null,
            'actif' => true,
            'peut_publier_commun' => false,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role_id' => Role::query()->where('libelle', 'admin')->value('id')]);
    }

    public function enseignant(): static
    {
        return $this->state(fn () => ['role_id' => Role::query()->where('libelle', 'enseignant')->value('id')]);
    }

    public function eleve(): static
    {
        return $this->state(fn () => ['role_id' => Role::query()->where('libelle', 'eleve')->value('id')]);
    }

    public function inactif(): static
    {
        return $this->state(fn () => ['actif' => false]);
    }
}
