<?php

namespace App\Services;

use App\Models\Manuel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileService
{
    private const MIME_PDF = ['application/pdf'];

    private const MIME_EPUB = ['application/epub+zip'];
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

    /**
     * Detecte le type reel du fichier via son contenu (finfo/libmagic), pas
     * son extension ni le Content-Type declare par le client (facilement
     * falsifiable) - exigence de la section 9 du cahier des charges.
     * Retourne Manuel::TYPE_PDF ou Manuel::TYPE_EPUB, ou null si le fichier
     * n'est ni l'un ni l'autre.
     */
    public function detecterType(UploadedFile $fichier): ?string
    {
        $mime = $fichier->getMimeType();

        if (in_array($mime, self::MIME_PDF, true)) {
            return Manuel::TYPE_PDF;
        }

        if (in_array($mime, self::MIME_EPUB, true)) {
            return Manuel::TYPE_EPUB;
        }

        return null;
    }

    /**
     * Stocke le fichier manuel avec un nom assaini (UUID), jamais le nom
     * original fourni par l'utilisateur (protection contre l'injection de
     * chemin / caracteres dangereux).
     */
    public function storerManuel(UploadedFile $fichier, string $type): string
    {
        $extension = $type === Manuel::TYPE_PDF ? 'pdf' : 'epub';
        $nom = Str::uuid()->toString().'.'.$extension;

        Storage::disk('manuels')->put($nom, file_get_contents($fichier->getRealPath()));

        return $nom;
    }

    public function storerCouverture(UploadedFile $image): string
    {
        $nom = Str::uuid()->toString().'.'.$image->extension();

        Storage::disk('couvertures')->put($nom, file_get_contents($image->getRealPath()));

        return $nom;
    }

    public function supprimerFichiers(Manuel $manuel): void
    {
        $this->supprimerFichierManuel($manuel);
        $this->supprimerCouverture($manuel);
    }

    public function supprimerFichierManuel(Manuel $manuel): void
    {
        if ($manuel->fichier) {
            Storage::disk('manuels')->delete($manuel->fichier);
        }
    }

    public function supprimerCouverture(Manuel $manuel): void
    {
        if ($manuel->couverture) {
            Storage::disk('couvertures')->delete($manuel->couverture);
        }
    }
}
