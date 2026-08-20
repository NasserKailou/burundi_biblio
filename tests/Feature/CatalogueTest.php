<?php

namespace Tests\Feature;

use App\Models\Manuel;
use App\Models\Niveau;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_api_manuels_ne_retourne_que_le_catalogue_filtre_pour_l_eleve(): void
    {
        $niveau = Niveau::factory()->create();
        $autreNiveau = Niveau::factory()->create();
        $eleve = User::factory()->eleve()->create(['niveau_id' => $niveau->id]);

        $manuelVisible = Manuel::factory()->create();
        $manuelVisible->niveaux()->attach($niveau);

        $manuelInvisible = Manuel::factory()->create();
        $manuelInvisible->niveaux()->attach($autreNiveau);

        $reponse = $this->actingAs($eleve)->getJson('/api/manuels');

        $reponse->assertOk();
        $ids = collect($reponse->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($manuelVisible->id));
        $this->assertFalse($ids->contains($manuelInvisible->id));
    }

    public function test_un_eleve_recoit_403_sur_la_fiche_d_un_manuel_hors_de_son_niveau(): void
    {
        $niveau = Niveau::factory()->create();
        $autreNiveau = Niveau::factory()->create();
        $eleve = User::factory()->eleve()->create(['niveau_id' => $niveau->id]);

        $manuelHorsPerimetre = Manuel::factory()->create();
        $manuelHorsPerimetre->niveaux()->attach($autreNiveau);

        $this->actingAs($eleve)->get("/catalogue/{$manuelHorsPerimetre->id}")->assertForbidden();
    }

    public function test_un_eleve_recoit_403_sur_un_manuel_en_brouillon(): void
    {
        $niveau = Niveau::factory()->create();
        $eleve = User::factory()->eleve()->create(['niveau_id' => $niveau->id]);

        $manuelBrouillon = Manuel::factory()->brouillon()->create();
        $manuelBrouillon->niveaux()->attach($niveau);

        $this->actingAs($eleve)->get("/catalogue/{$manuelBrouillon->id}")->assertForbidden();
    }

    public function test_recherche_ajax_filtre_par_mot_cle(): void
    {
        $niveau = Niveau::factory()->create();
        $eleve = User::factory()->eleve()->create(['niveau_id' => $niveau->id]);

        $manuelCorrespondant = Manuel::factory()->commun()->create(['titre' => 'Grammaire Francaise Avancee']);
        $manuelNonCorrespondant = Manuel::factory()->commun()->create(['titre' => 'Algebre Lineaire']);

        $reponse = $this->actingAs($eleve)->getJson('/api/manuels?q=Grammaire');

        $ids = collect($reponse->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($manuelCorrespondant->id));
        $this->assertFalse($ids->contains($manuelNonCorrespondant->id));
    }
}
