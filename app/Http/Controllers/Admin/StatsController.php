<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use App\Services\StatsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class StatsController extends Controller
{
    public function __construct(private readonly StatsService $stats)
    {
    }

    public function index(Request $request): View
    {
        $niveauIds = $request->filled('niveau') ? [$request->integer('niveau')] : null;
        $granularite = $request->string('granularite')->value() ?: 'jour';

        return view('admin.statistiques.index', [
            'niveaux' => Niveau::query()->orderBy('ordre')->get(),
            'niveauSelectionne' => $request->integer('niveau'),
            'granularite' => $granularite,
            'overview' => $this->stats->overview($niveauIds),
            'manuelsPlusConsultes' => $this->stats->manuelsPlusConsultes($niveauIds),
            'elevesPlusActifs' => $this->stats->elevesPlusActifs($niveauIds),
            'enseignantsPlusActifs' => $this->stats->enseignantsPlusActifs(),
            'consultationsParPeriode' => $this->stats->consultationsParPeriode($granularite, $niveauIds),
            'repartitionMatiere' => $this->stats->repartitionParMatiere($niveauIds),
            'repartitionNiveau' => $this->stats->repartitionParNiveau(),
        ]);
    }

    public function export(Request $request): Response
    {
        $format = $request->string('format')->value() ?: 'csv';
        $niveauIds = $request->filled('niveau') ? [$request->integer('niveau')] : null;

        $donnees = [
            'overview' => $this->stats->overview($niveauIds),
            'manuelsPlusConsultes' => $this->stats->manuelsPlusConsultes($niveauIds, 50),
            'elevesPlusActifs' => $this->stats->elevesPlusActifs($niveauIds, 50),
        ];

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.statistiques.export-pdf', $donnees);

            return $pdf->download('statistiques-bns.pdf');
        }

        return response()->streamDownload(function () use ($donnees) {
            $sortie = fopen('php://output', 'w');
            fputcsv($sortie, ['Manuel', 'Consultations', 'Duree (secondes)']);
            foreach ($donnees['manuelsPlusConsultes'] as $ligne) {
                fputcsv($sortie, [$ligne['titre'], $ligne['nb_consultations'], $ligne['duree_secondes']]);
            }
            fputcsv($sortie, []);
            fputcsv($sortie, ['Eleve', 'Niveau', 'Consultations', 'Duree (secondes)']);
            foreach ($donnees['elevesPlusActifs'] as $ligne) {
                fputcsv($sortie, [$ligne['nom'], $ligne['niveau'], $ligne['nb_consultations'], $ligne['duree_secondes']]);
            }
            fclose($sortie);
        }, 'statistiques-bns.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
