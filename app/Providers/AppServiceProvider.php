<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gates consommees par le menu AdminLTE (config/adminlte.php, cle 'can') pour
        // filtrer la sidebar par role au rendu - evaluees dynamiquement, donc
        // compatibles avec `php artisan config:cache`.
        Gate::define('is-admin', fn ($user) => $user->isAdmin());
        Gate::define('is-enseignant', fn ($user) => $user->isEnseignant());
        Gate::define('is-eleve', fn ($user) => $user->isEleve());
    }
}
