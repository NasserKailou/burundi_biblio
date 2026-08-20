<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Manuel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $nbManuels = Manuel::query()->where('uploader_id', $user->id)->count();
        $nbPublies = Manuel::query()->where('uploader_id', $user->id)->where('statut', Manuel::STATUT_PUBLIE)->count();

        return view('dashboard.enseignant', [
            'user' => $user,
            'nbManuels' => $nbManuels,
            'nbPublies' => $nbPublies,
        ]);
    }
}
