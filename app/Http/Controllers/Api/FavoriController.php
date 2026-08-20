<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Manuel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriController extends Controller
{
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

        $request->user()->favoris()->firstOrCreate(['manuel_id' => $manuel->id]);

        return response()->json(['data' => ['favori' => true]], 201);
    }

    public function destroy(Request $request, Manuel $manuel): JsonResponse
    {
        $request->user()->favoris()->where('manuel_id', $manuel->id)->delete();

        return response()->json(['data' => ['favori' => false]]);
    }
}
