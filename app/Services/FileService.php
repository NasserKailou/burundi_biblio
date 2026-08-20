<?php

namespace App\Services;

use App\Models\Manuel;
use Illuminate\Support\Facades\Storage;
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
}
