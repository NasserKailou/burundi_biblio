<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Manuel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Enregistre l'ouverture d'un manuel par l'utilisateur courant
     * (declenche a l'ouverture du lecteur).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'manuel_id' => ['required', 'integer', 'exists:manuels,id'],
        ]);

        $manuel = Manuel::query()->findOrFail($data['manuel_id']);

        $visible = Manuel::query()
            ->visiblePour($request->user())
            ->whereKey($manuel->id)
            ->exists();

        abort_unless($visible, 403);

        $consultation = Consultation::query()->create([
            'user_id' => $request->user()->id,
            'manuel_id' => $manuel->id,
            'date_ouverture' => now(),
            'duree_secondes' => 0,
        ]);

        return response()->json(['data' => ['id' => $consultation->id]], 201);
    }

    /**
     * Met a jour la duree de lecture et la derniere page/position lue.
     * Appelee periodiquement et a la fermeture du lecteur.
     */
    public function update(Request $request, Consultation $consultation): JsonResponse
    {
        abort_unless($consultation->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'duree_secondes' => ['required', 'integer', 'min:0', 'max:86400'],
            'derniere_page' => ['nullable', 'integer', 'min:1'],
        ]);

        $consultation->update($data);

        return response()->json(['data' => ['id' => $consultation->id]]);
    }
}
