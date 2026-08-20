<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatsController extends Controller
{
    public function __construct(private readonly StatsService $stats)
    {
    }

    public function index(Request $request): View
    {
        $niveauIds = $request->user()->idsNiveauxGeres();

        return view('teacher.statistiques.index', [
            'overview' => $this->stats->overview($niveauIds),
            'manuelsPlusConsultes' => $this->stats->manuelsPlusConsultes($niveauIds),
            'elevesPlusActifs' => $this->stats->elevesPlusActifs($niveauIds),
            'consultationsParPeriode' => $this->stats->consultationsParPeriode('jour', $niveauIds),
            'repartitionMatiere' => $this->stats->repartitionParMatiere($niveauIds),
        ]);
    }
}
