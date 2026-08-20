<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Securite : chaque route protegee par role doit renvoyer 403 aux autres
 * roles (section 9 - "toute tentative non autorisee -> 403").
 */
class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_un_eleve_ne_peut_pas_acceder_au_back_office_admin(): void
    {
        $eleve = User::factory()->eleve()->create();

        $this->actingAs($eleve)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($eleve)->get('/admin/utilisateurs')->assertForbidden();
    }

    public function test_un_eleve_ne_peut_pas_acceder_a_l_espace_enseignant(): void
    {
        $eleve = User::factory()->eleve()->create();

        $this->actingAs($eleve)->get('/teacher/dashboard')->assertForbidden();
        $this->actingAs($eleve)->get('/teacher/manuels')->assertForbidden();
    }

    public function test_un_enseignant_ne_peut_pas_acceder_au_back_office_admin(): void
    {
        $enseignant = User::factory()->enseignant()->create();

        $this->actingAs($enseignant)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($enseignant)->get('/admin/statistiques')->assertForbidden();
    }

    public function test_un_enseignant_ne_peut_pas_acceder_au_catalogue_eleve(): void
    {
        $enseignant = User::factory()->enseignant()->create();

        $this->actingAs($enseignant)->get('/catalogue')->assertForbidden();
    }

    public function test_un_admin_ne_peut_pas_acceder_au_dashboard_eleve(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/dashboard')->assertForbidden();
    }

    public function test_un_visiteur_non_connecte_est_redirige_vers_la_connexion(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/catalogue')->assertRedirect('/login');
    }
}
