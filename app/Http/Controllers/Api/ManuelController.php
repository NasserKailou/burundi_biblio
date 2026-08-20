<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Manuel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManuelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Manuel::query()
            ->visiblePour($request->user())
            ->with(['matiere', 'niveaux'])
            ->orderBy('titre');

        if ($matiereId = $request->integer('matiere')) {
            $query->where('matiere_id', $matiereId);
        }

        if ($q = trim((string) $request->string('q'))) {
            $query->where(function ($query) use ($q) {
                $query->where('titre', 'like', "%{$q}%")
                    ->orWhere('auteur', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $manuels = $query->paginate(24);

        return response()->json([
            'data' => $manuels->getCollection()->map(fn (Manuel $manuel) => $this->transform($manuel)),
            'meta' => [
                'current_page' => $manuels->currentPage(),
                'last_page' => $manuels->lastPage(),
                'total' => $manuels->total(),
            ],
        ]);
    }

    public function show(Request $request, Manuel $manuel): JsonResponse
    {
        $visible = Manuel::query()
            ->visiblePour($request->user())
            ->whereKey($manuel->id)
            ->exists();

        abort_unless($visible, 403);

        $manuel->load(['matiere', 'niveaux']);

        return response()->json(['data' => $this->transform($manuel, avecDescription: true)]);
    }

    private function transform(Manuel $manuel, bool $avecDescription = false): array
    {
        $data = [
            'id' => $manuel->id,
            'titre' => $manuel->titre,
            'auteur' => $manuel->auteur,
            'annee' => $manuel->annee,
            'type' => $manuel->type,
            'est_commun' => $manuel->est_commun,
            'matiere' => $manuel->matiere->libelle,
            'niveaux' => $manuel->niveaux->pluck('libelle'),
            'couverture_url' => route('catalogue.couverture', $manuel),
            'fiche_url' => route('catalogue.show', $manuel),
        ];

        if ($avecDescription) {
            $data['description'] = $manuel->description;
        }

        return $data;
    }
}
