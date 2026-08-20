<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manuel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard.admin', [
            'user' => $request->user(),
            'nbUtilisateurs' => User::query()->count(),
            'nbUtilisateursEnAttente' => User::query()->where('actif', false)->count(),
            'nbManuels' => Manuel::query()->count(),
        ]);
    }
}
