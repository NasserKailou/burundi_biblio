<?php

namespace Tests\Feature;

use App\Models\Niveau;
use App\Models\Parametre;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_un_eleve_actif_peut_se_connecter_avec_son_identifiant(): void
    {
        $eleve = User::factory()->eleve()->create(['identifiant' => 'eleve_test', 'password' => Hash::make('MotDePasse@2026')]);

        $reponse = $this->post('/login', [
            'identifiant' => 'eleve_test',
            'password' => 'MotDePasse@2026',
        ]);

        $reponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($eleve);
    }

    public function test_un_compte_inactif_est_rejete_a_la_connexion(): void
    {
        $eleve = User::factory()->eleve()->inactif()->create(['identifiant' => 'eleve_attente', 'password' => Hash::make('MotDePasse@2026')]);

        $reponse = $this->post('/login', [
            'identifiant' => 'eleve_attente',
            'password' => 'MotDePasse@2026',
        ]);

        $reponse->assertSessionHasErrors('identifiant');
        $this->assertGuest();
    }

    public function test_un_mot_de_passe_incorrect_est_rejete(): void
    {
        User::factory()->eleve()->create(['identifiant' => 'eleve_test', 'password' => Hash::make('BonMotDePasse@2026')]);

        $reponse = $this->post('/login', [
            'identifiant' => 'eleve_test',
            'password' => 'MauvaisMotDePasse',
        ]);

        $reponse->assertSessionHasErrors('identifiant');
        $this->assertGuest();
    }

    public function test_inscription_cree_un_compte_inactif_par_defaut(): void
    {
        Parametre::query()->create(['cle' => 'validation_auto', 'valeur' => 'false']);
        Parametre::query()->create(['cle' => 'politique_mdp_longueur_min', 'valeur' => '8']);
        $niveau = Niveau::factory()->create();

        $reponse = $this->post('/inscription', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'identifiant' => 'jean_dupont',
            'niveau_id' => $niveau->id,
            'password' => 'MotDePasse@2026',
            'password_confirmation' => 'MotDePasse@2026',
        ]);

        $reponse->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseHas('users', ['identifiant' => 'jean_dupont', 'actif' => false]);
    }

    public function test_inscription_active_immediatement_si_validation_auto_est_activee(): void
    {
        Parametre::query()->create(['cle' => 'validation_auto', 'valeur' => 'true']);
        Parametre::query()->create(['cle' => 'politique_mdp_longueur_min', 'valeur' => '8']);
        $niveau = Niveau::factory()->create();

        $reponse = $this->post('/inscription', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'identifiant' => 'jean_dupont',
            'niveau_id' => $niveau->id,
            'password' => 'MotDePasse@2026',
            'password_confirmation' => 'MotDePasse@2026',
        ]);

        $reponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['identifiant' => 'jean_dupont', 'actif' => true]);
    }
}
