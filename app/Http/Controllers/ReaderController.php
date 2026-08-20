<?php

namespace App\Http\Controllers;

use App\Models\Manuel;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReaderController extends Controller
{
    public function show(Request $request, Manuel $manuel): View
    {
        $visible = Manuel::query()
            ->visiblePour($request->user())
            ->whereKey($manuel->id)
            ->exists();

        abort_unless($visible, 403);

        $derniereConsultation = $request->user()->consultations()
            ->where('manuel_id', $manuel->id)
            ->latest('date_ouverture')
            ->first();

        $estFavori = $request->user()->favoris()->where('manuel_id', $manuel->id)->exists();

        return view('reader.show', [
            'manuel' => $manuel,
            'dernierePage' => $derniereConsultation?->derniere_page,
            'estFavori' => $estFavori,
        ]);
    }

    /**
     * $nom n'est pas utilise : il sert uniquement a donner a l'URL une
     * extension .pdf/.epub (voir routes/web.php).
     */
    public function fichier(Request $request, Manuel $manuel, string $nom, FileService $files): BinaryFileResponse
    {
        $visible = Manuel::query()
            ->visiblePour($request->user())
            ->whereKey($manuel->id)
            ->exists();

        abort_unless($visible, 403);

        return $files->streamManuel($manuel);
    }
}
