<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_un_admin_peut_valider_l_inscription_d_un_eleve_en_attente(): void
    {
        $admin = User::factory()->admin()->create();
        $eleveEnAttente = User::factory()->eleve()->inactif()->create();

        $this->actingAs($admin)->post("/admin/utilisateurs/{$eleveEnAttente->id}/activer")->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $eleveEnAttente->id, 'actif' => true]);
    }

    public function test_impossible_de_supprimer_le_dernier_administrateur(): void
    {
        $roleAdmin = Role::query()->where('libelle', 'admin')->firstOrFail();
        $seulAdmin = User::factory()->admin()->create();

        $this->assertSame(1, User::query()->where('role_id', $roleAdmin->id)->count());

        $this->actingAs($seulAdmin)->delete("/admin/utilisateurs/{$seulAdmin->id}")->assertStatus(422);

        $this->assertDatabaseHas('users', ['id' => $seulAdmin->id]);
    }

    public function test_la_suppression_reussit_quand_il_reste_un_autre_administrateur(): void
    {
        $admin1 = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();

        $this->actingAs($admin1)->delete("/admin/utilisateurs/{$admin2->id}")->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $admin2->id]);
    }

    public function test_reinitialiser_le_mot_de_passe_change_bien_le_hash(): void
    {
        $admin = User::factory()->admin()->create();
        $eleve = User::factory()->eleve()->create();
        $hashInitial = $eleve->password;

        $this->actingAs($admin)->post("/admin/utilisateurs/{$eleve->id}/reinitialiser-mot-de-passe")->assertRedirect();

        $this->assertNotSame($hashInitial, $eleve->fresh()->password);
    }
}
