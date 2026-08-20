<?php

namespace App\Services;

use App\Models\Manuel;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileService
{
    /**
     * Diffuse la couverture d'un manuel. Les couvertures ne sont pas
     * sensibles (contrairement au contenu des manuels) mais restent
     * servies via un disque prive + controleur pour rester coherentes
     * avec le reste du stockage (rien n'est expose directement par nginx).
     */
    public function streamCouverture(Manuel $manuel): StreamedResponse
    {
        abort_unless(
            $manuel->couverture && Storage::disk('couvertures')->exists($manuel->couverture),
            404
        );

        return Storage::disk('couvertures')->response($manuel->couverture, null, [
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Diffuse le fichier PDF/EPUB du manuel pour le lecteur. Le disque
     * "manuels" est prive (hors racine web), l'acces passe systematiquement
     * par ce controleur apres verification RBAC (jamais de lien direct).
     *
     * Utilise response()->file() (BinaryFileResponse) et non
     * Storage::response() (StreamedResponse) : PDF.js a besoin de requetes
     * HTTP Range (206 Partial Content) pour charger un PDF par morceaux -
     * StreamedResponse renvoie systematiquement le fichier entier en 200,
     * ce qui bloquait le rendu du lecteur (constate en test navigateur reel).
     */
    public function streamManuel(Manuel $manuel): BinaryFileResponse
    {
        abort_unless(Storage::disk('manuels')->exists($manuel->fichier), 404);

        $mime = $manuel->type === Manuel::TYPE_PDF ? 'application/pdf' : 'application/epub+zip';
        $chemin = Storage::disk('manuels')->path($manuel->fichier);

        return response()->file($chemin, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
