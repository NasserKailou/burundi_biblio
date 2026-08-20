<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sauvegarde quotidienne (BDD + fichiers manuels/couvertures) - section 11
// du cahier des charges. Execute par le service "scheduler" du
// docker-compose (boucle appelant "php artisan schedule:run" chaque
// minute - il n'y a pas de cron systeme dans le conteneur app).
Schedule::exec('sh '.base_path('scripts/sauvegarde.sh'))
    ->dailyAt('02:00')
    ->onOneServer();

// Minimisation des donnees d'eleves mineurs (section 9) : purge
// l'historique de lecture au-dela de la duree de conservation configuree.
Schedule::command('bns:purger-consultations')
    ->weekly()
    ->onOneServer();
