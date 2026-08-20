<?php

namespace App\Http\Controllers;

use App\Models\Manuel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $manuelsEnCours = Manuel::query()
            ->whereHas('consultations', fn ($q) => $q->where('user_id', $user->id))
            ->withMax(['consultations as derniere_lecture' => fn ($q) => $q->where('user_id', $user->id)], 'date_ouverture')
            ->orderByDesc('derniere_lecture')
            ->limit(4)
            ->get();

        $manuelsFavoris = Manuel::query()
            ->whereHas('favoris', fn ($q) => $q->where('user_id', $user->id))
            ->limit(8)
            ->get();

        return view('dashboard.eleve', [
            'user' => $user,
            'manuelsEnCours' => $manuelsEnCours,
            'manuelsFavoris' => $manuelsFavoris,
        ]);
    }
}
