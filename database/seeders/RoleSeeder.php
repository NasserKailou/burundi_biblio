<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'enseignant', 'eleve'] as $libelle) {
            Role::query()->firstOrCreate(['libelle' => $libelle]);
        }
    }
}
