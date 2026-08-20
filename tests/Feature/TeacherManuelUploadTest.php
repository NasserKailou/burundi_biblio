<?php

namespace Tests\Feature;

use App\Models\Manuel;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeacherManuelUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        Storage::fake('manuels');
        Storage::fake('couvertures');
    }

    public function test_un_enseignant_peut_televerser_un_manuel_valide(): void
    {
        $niveau = Niveau::factory()->create();
        $matiere = Matiere::factory()->create();
        $enseignant = User::factory()->enseignant()->create(['niveau_id' => $niveau->id]);

        $reponse = $this->actingAs($enseignant)->post('/teacher/manuels', [
            'titre' => 'Manuel de test',
            'matiere_id' => $matiere->id,
            'niveaux' => [$niveau->id],
            'statut' => 'publie',
            'fichier' => UploadedFile::fake()->create('manuel.pdf', 500, 'application/pdf'),
            'couverture' => UploadedFile::fake()->image('couverture.jpg'),
        ]);

        $reponse->assertRedirect(route('teacher.manuels.index'));
        $this->assertDatabaseHas('manuels', [
            'titre' => 'Manuel de test',
            'uploader_id' => $enseignant->id,
            'type' => 'pdf',
        ]);
    }

    public function test_un_fichier_dont_le_contenu_ne_correspond_pas_a_l_extension_est_rejete(): void
    {
        $niveau = Niveau::factory()->create();
        $matiere = Matiere::factory()->create();
        $enseignant = User::factory()->enseignant()->create(['niveau_id' => $niveau->id]);

        $reponse = $this->actingAs($enseignant)->post('/teacher/manuels', [
            'titre' => 'Manuel invalide',
            'matiere_id' => $matiere->id,
            'niveaux' => [$niveau->id],
            'statut' => 'publie',
            // Extension .pdf mais type MIME reel declare text/plain : doit etre rejete
            // par la detection du contenu reel, pas seulement l'extension.
            'fichier' => UploadedFile::fake()->create('manuel.pdf', 10, 'text/plain'),
            'couverture' => UploadedFile::fake()->image('couverture.jpg'),
        ]);

        $reponse->assertStatus(422);
        $this->assertDatabaseMissing('manuels', ['titre' => 'Manuel invalide']);
    }

    public function test_un_enseignant_ne_peut_pas_cibler_un_niveau_qu_il_ne_gere_pas(): void
    {
        $sonNiveau = Niveau::factory()->create();
        $niveauNonGere = Niveau::factory()->create();
        $matiere = Matiere::factory()->create();
        $enseignant = User::factory()->enseignant()->create(['niveau_id' => $sonNiveau->id]);

        $reponse = $this->actingAs($enseignant)->post('/teacher/manuels', [
            'titre' => 'Manuel hors perimetre',
            'matiere_id' => $matiere->id,
            'niveaux' => [$niveauNonGere->id],
            'statut' => 'publie',
            'fichier' => UploadedFile::fake()->create('manuel.pdf', 500, 'application/pdf'),
            'couverture' => UploadedFile::fake()->image('couverture.jpg'),
        ]);

        $reponse->assertSessionHasErrors('niveaux.0');
    }

    public function test_un_enseignant_ne_peut_pas_modifier_le_manuel_d_un_collegue(): void
    {
        $niveau = Niveau::factory()->create();
        $enseignant = User::factory()->enseignant()->create(['niveau_id' => $niveau->id]);
        $collegue = User::factory()->enseignant()->create(['niveau_id' => $niveau->id]);

        $manuelDuCollegue = Manuel::factory()->create(['uploader_id' => $collegue->id]);

        $this->actingAs($enseignant)->get("/teacher/manuels/{$manuelDuCollegue->id}/modifier")->assertForbidden();
        $this->actingAs($enseignant)->delete("/teacher/manuels/{$manuelDuCollegue->id}")->assertForbidden();
        $this->assertDatabaseHas('manuels', ['id' => $manuelDuCollegue->id]);
    }
}
