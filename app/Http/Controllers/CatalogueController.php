<?php

namespace App\Http\Controllers;

use App\Models\Manuel;
use App\Models\Matiere;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogueController extends Controller
{
    public function index(Request $request): View
    {
        return view('catalogue.index', [
            'matieres' => Matiere::query()->orderBy('libelle')->get(),
        ]);
    }

    public function show(Request $request, Manuel $manuel): View
    {
        $visible = Manuel::query()
            ->visiblePour($request->user())
            ->whereKey($manuel->id)
            ->exists();

        abort_unless($visible, 403);

        $manuel->load(['matiere', 'niveaux', 'uploader']);

        $estFavori = $request->user()->favoris()->where('manuel_id', $manuel->id)->exists();

        return view('catalogue.show', [
            'manuel' => $manuel,
            'estFavori' => $estFavori,
        ]);
    }

    public function couverture(Request $request, Manuel $manuel, FileService $files): StreamedResponse
    {
        $visible = Manuel::query()
            ->visiblePour($request->user())
            ->whereKey($manuel->id)
            ->exists();

        abort_unless($visible, 403);

        return $files->streamCouverture($manuel);
    }
}
