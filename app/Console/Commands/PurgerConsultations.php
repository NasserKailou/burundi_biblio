<?php

namespace App\Console\Commands;

use App\Models\Consultation;
use App\Models\Parametre;
use App\Services\AuditService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Minimisation des donnees d'eleves mineurs (section 9 du cahier des
 * charges) : l'historique de lecture detaille (consultations) n'est
 * conserve que "duree_conservation_consultations_jours" (parametre
 * configurable par l'admin, defaut 730 jours). Ne supprime jamais les
 * comptes ni le catalogue, uniquement l'historique de lecture ancien.
 */
class PurgerConsultations extends Command
{
    protected $signature = 'bns:purger-consultations {--dry-run : Affiche ce qui serait supprime sans rien supprimer}';

    protected $description = "Purge les consultations plus anciennes que la duree de conservation configuree (donnees d'eleves mineurs)";

    public function handle(AuditService $audit): int
    {
        $jours = (int) (Parametre::get('duree_conservation_consultations_jours') ?? 730);
        $seuil = Carbon::now()->subDays($jours);

        $requete = Consultation::query()->where('date_ouverture', '<', $seuil);
        $nombre = $requete->count();

        if ($this->option('dry-run')) {
            $this->info("{$nombre} consultation(s) anterieure(s) au {$seuil->format('d/m/Y')} seraient supprimees (dry-run, rien n'a ete modifie).");

            return self::SUCCESS;
        }

        $requete->delete();

        $audit->log('purge_consultations', "{$nombre} ligne(s) anterieure(s) au {$seuil->format('Y-m-d')}");

        $this->info("{$nombre} consultation(s) anterieure(s) au {$seuil->format('d/m/Y')} supprimee(s).");

        return self::SUCCESS;
    }
}
