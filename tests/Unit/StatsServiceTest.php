<?php

namespace Tests\Unit;

use App\Models\Consultation;
use App\Models\Manuel;
use App\Models\Niveau;
use App\Models\User;
use App\Services\StatsService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsServiceTest extends TestCase
{
    use RefreshDatabase;

    private StatsService $stats;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->stats = new StatsService();
    }

    public function test_overview_compte_les_consultations_et_eleves_actifs(): void
    {
        $eleve = User::factory()->eleve()->create();
        $manuel = Manuel::factory()->create();

        Consultation::factory()->count(3)->create([
            'user_id' => $eleve->id,
            'manuel_id' => $manuel->id,
            'duree_secondes' => 1200,
        ]);

        $overview = $this->stats->overview();

        $this->assertSame(1, $overview['nb_manuels']);
        $this->assertSame(3, $overview['nb_consultations']);
        $this->assertSame(1, $overview['nb_eleves_actifs']);
        $this->assertSame(1.0, $overview['duree_totale_heures']);
    }

    public function test_manuels_plus_consultes_est_trie_par_nombre_de_consultations(): void
    {
        $peuConsulte = Manuel::factory()->create(['titre' => 'Peu lu']);
        $trePopulaire = Manuel::factory()->create(['titre' => 'Tres lu']);
        $eleve = User::factory()->eleve()->create();

        Consultation::factory()->count(1)->create(['manuel_id' => $peuConsulte->id, 'user_id' => $eleve->id]);
        Consultation::factory()->count(5)->create(['manuel_id' => $trePopulaire->id, 'user_id' => $eleve->id]);

        $resultat = $this->stats->manuelsPlusConsultes();

        $this->assertSame('Tres lu', $resultat->first()['titre']);
        $this->assertSame(5, $resultat->first()['nb_consultations']);
    }

    public function test_overview_est_restreinte_aux_niveaux_fournis(): void
    {
        $niveau6e = Niveau::factory()->create();
        $niveauAutre = Niveau::factory()->create();

        $manuel6e = Manuel::factory()->create();
        $manuel6e->niveaux()->attach($niveau6e);

        $manuelAutre = Manuel::factory()->create();
        $manuelAutre->niveaux()->attach($niveauAutre);

        $overviewRestreinte = $this->stats->overview([$niveau6e->id]);
        $overviewGlobale = $this->stats->overview();

        $this->assertSame(1, $overviewRestreinte['nb_manuels']);
        $this->assertSame(2, $overviewGlobale['nb_manuels']);
    }
}
