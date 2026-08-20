<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\Manuel;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Portee : quand $niveauIds est fourni (non null), toutes les requetes sont
 * restreintes a ces niveaux - utilise pour un enseignant (section 6.5 :
 * "portee selon role : admin = global, enseignant = son niveau"). null
 * signifie une portee globale (admin uniquement).
 */
class StatsService
{
    public function overview(?array $niveauIds = null): array
    {
        $manuelsQuery = Manuel::query()->when($niveauIds, fn ($q) => $this->limiterAuxNiveaux($q, $niveauIds));
        $consultationsQuery = Consultation::query()->when($niveauIds, fn ($q) => $q->whereHas('manuel', fn ($m) => $this->limiterAuxNiveaux($m, $niveauIds)));

        return [
            'nb_manuels' => (clone $manuelsQuery)->count(),
            'nb_consultations' => (clone $consultationsQuery)->count(),
            'duree_totale_heures' => round(((clone $consultationsQuery)->sum('duree_secondes')) / 3600, 1),
            'nb_eleves_actifs' => (clone $consultationsQuery)->distinct('user_id')->count('user_id'),
        ];
    }

    public function manuelsPlusConsultes(?array $niveauIds = null, int $limite = 10): Collection
    {
        return Manuel::query()
            ->when($niveauIds, fn ($q) => $this->limiterAuxNiveaux($q, $niveauIds))
            ->withCount('consultations')
            ->withSum('consultations', 'duree_secondes')
            ->orderByDesc('consultations_count')
            ->limit($limite)
            ->get()
            ->map(fn (Manuel $m) => [
                'titre' => $m->titre,
                'nb_consultations' => $m->consultations_count,
                'duree_secondes' => (int) $m->consultations_sum_duree_secondes,
            ]);
    }

    public function elevesPlusActifs(?array $niveauIds = null, int $limite = 10): Collection
    {
        return User::query()
            ->whereHas('role', fn ($r) => $r->where('libelle', 'eleve'))
            ->when($niveauIds, fn ($q) => $q->whereIn('niveau_id', $niveauIds))
            ->withCount('consultations')
            ->withSum('consultations', 'duree_secondes')
            ->orderByDesc('consultations_count')
            ->limit($limite)
            ->get()
            ->map(fn (User $u) => [
                'nom' => $u->nomComplet(),
                'niveau' => $u->niveau?->libelle,
                'nb_consultations' => $u->consultations_count,
                'duree_secondes' => (int) $u->consultations_sum_duree_secondes,
            ]);
    }

    /**
     * Un enseignant est "actif" via l'usage genere par les manuels qu'il a
     * publies (nombre de consultations recues), pas ses propres lectures.
     */
    public function enseignantsPlusActifs(int $limite = 10): Collection
    {
        return User::query()
            ->whereHas('role', fn ($r) => $r->where('libelle', 'enseignant'))
            ->withCount('manuelsPublies')
            ->withCount(['manuelsPublies as consultations_recues_count' => function ($q) {
                $q->join('consultations', 'consultations.manuel_id', '=', 'manuels.id');
            }])
            ->orderByDesc('consultations_recues_count')
            ->limit($limite)
            ->get()
            ->map(fn (User $u) => [
                'nom' => $u->nomComplet(),
                'nb_manuels' => $u->manuels_publies_count,
                'nb_consultations_recues' => $u->consultations_recues_count,
            ]);
    }

    /**
     * Regroupement fait cote PHP (via Carbon) plutot qu'en SQL brut
     * (strftime/DATE_FORMAT) pour rester agnostique du SGBD (SQLite en
     * dev/tests, MySQL/MariaDB en production - section 2 du cahier des
     * charges). Volumetrie d'un intranet d'etablissement : largement
     * suffisant en performance.
     */
    public function consultationsParPeriode(string $granularite = 'jour', ?array $niveauIds = null, int $jours = 30): Collection
    {
        $dates = Consultation::query()
            ->when($niveauIds, fn ($q) => $q->whereHas('manuel', fn ($m) => $this->limiterAuxNiveaux($m, $niveauIds)))
            ->where('date_ouverture', '>=', Carbon::now()->subDays($jours))
            ->pluck('date_ouverture');

        $format = match ($granularite) {
            'semaine' => fn (Carbon $d) => $d->startOfWeek()->format('Y-m-d'),
            'mois' => fn (Carbon $d) => $d->format('Y-m'),
            default => fn (Carbon $d) => $d->format('Y-m-d'),
        };

        return $dates
            ->map(fn ($date) => $format(Carbon::parse($date)))
            ->countBy()
            ->sortKeys()
            ->map(fn ($total, $periode) => ['periode' => $periode, 'total' => $total])
            ->values();
    }

    public function repartitionParMatiere(?array $niveauIds = null): Collection
    {
        return Manuel::query()
            ->when($niveauIds, fn ($q) => $this->limiterAuxNiveaux($q, $niveauIds))
            ->join('matieres', 'matieres.id', '=', 'manuels.matiere_id')
            ->select('matieres.libelle', DB::raw('count(*) as total'))
            ->groupBy('matieres.libelle')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($ligne) => ['libelle' => $ligne->libelle, 'total' => (int) $ligne->total]);
    }

    public function repartitionParNiveau(): Collection
    {
        return DB::table('manuel_niveau')
            ->join('niveaux', 'niveaux.id', '=', 'manuel_niveau.niveau_id')
            ->select('niveaux.libelle', DB::raw('count(*) as total'))
            ->groupBy('niveaux.libelle', 'niveaux.ordre')
            ->orderBy('niveaux.ordre')
            ->get()
            ->map(fn ($ligne) => ['libelle' => $ligne->libelle, 'total' => (int) $ligne->total]);
    }

    private function limiterAuxNiveaux($query, array $niveauIds)
    {
        return $query->whereHas('niveaux', fn ($n) => $n->whereIn('niveaux.id', $niveauIds));
    }
}
