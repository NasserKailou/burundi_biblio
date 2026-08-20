<?php

namespace Tests\Unit;

use App\Models\Manuel;
use App\Models\Niveau;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regles de permission (Manuel::scopeVisiblePour) - filtrage cote requete,
 * jamais uniquement cote vue (section 5 du cahier des charges).
 */
class ManuelVisibiliteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_eleve_voit_les_manuels_de_son_niveau_et_les_communs_publies(): void
    {
        $niveau = Niveau::factory()->create();
        $autreNiveau = Niveau::factory()->create();
        $eleve = User::factory()->eleve()->create(['niveau_id' => $niveau->id]);

        $manuelDeSonNiveau = Manuel::factory()->create();
        $manuelDeSonNiveau->niveaux()->attach($niveau);

        $manuelCommun = Manuel::factory()->commun()->create();

        $manuelAutreNiveau = Manuel::factory()->create();
        $manuelAutreNiveau->niveaux()->attach($autreNiveau);

        $manuelBrouillonSonNiveau = Manuel::factory()->brouillon()->create();
        $manuelBrouillonSonNiveau->niveaux()->attach($niveau);

        $visibles = Manuel::query()->visiblePour($eleve)->pluck('id')->all();

        $this->assertContains($manuelDeSonNiveau->id, $visibles);
        $this->assertContains($manuelCommun->id, $visibles);
        $this->assertNotContains($manuelAutreNiveau->id, $visibles);
        $this->assertNotContains($manuelBrouillonSonNiveau->id, $visibles, 'Un brouillon ne doit jamais etre visible par un eleve.');
    }

    public function test_enseignant_voit_ses_propres_manuels_et_ceux_de_ses_niveaux_geres(): void
    {
        $niveauGere = Niveau::factory()->create();
        $niveauNonGere = Niveau::factory()->create();
        $enseignant = User::factory()->enseignant()->create(['niveau_id' => $niveauGere->id]);

        $sonManuel = Manuel::factory()->create(['uploader_id' => $enseignant->id]);

        $manuelCollegueMemeNiveau = Manuel::factory()->create();
        $manuelCollegueMemeNiveau->niveaux()->attach($niveauGere);

        $manuelHorsPerimetre = Manuel::factory()->create();
        $manuelHorsPerimetre->niveaux()->attach($niveauNonGere);

        $visibles = Manuel::query()->visiblePour($enseignant)->pluck('id')->all();

        $this->assertContains($sonManuel->id, $visibles);
        $this->assertContains($manuelCollegueMemeNiveau->id, $visibles);
        $this->assertNotContains($manuelHorsPerimetre->id, $visibles);
    }

    public function test_admin_voit_tout_y_compris_les_brouillons(): void
    {
        $admin = User::factory()->admin()->create();

        Manuel::factory()->count(2)->create();
        Manuel::factory()->brouillon()->create();

        $this->assertCount(3, Manuel::query()->visiblePour($admin)->get());
    }
}
