<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfigurationController extends Controller
{
    private const CLES_BOOLEENNES = ['validation_auto'];

    public function __construct(private readonly AuditService $audit)
    {
    }

    public function edit(): View
    {
        $cles = [
            'etablissement_nom', 'etablissement_logo', 'taille_max_fichier_mo',
            'formats_autorises', 'politique_mdp_longueur_min', 'validation_auto',
            'duree_conservation_consultations_jours',
        ];

        $parametres = [];
        foreach ($cles as $cle) {
            $parametres[$cle] = Parametre::get($cle);
        }

        return view('admin.configuration.edit', ['parametres' => $parametres]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'etablissement_nom' => ['required', 'string', 'max:255'],
            'etablissement_logo' => ['nullable', 'string', 'max:255'],
            'taille_max_fichier_mo' => ['required', 'integer', 'min:1', 'max:1000'],
            'formats_autorises' => ['required', 'string', 'max:100'],
            'politique_mdp_longueur_min' => ['required', 'integer', 'min:6', 'max:64'],
            'duree_conservation_consultations_jours' => ['required', 'integer', 'min:30'],
        ]);

        foreach ($data as $cle => $valeur) {
            Parametre::set($cle, (string) $valeur);
        }

        Parametre::set('validation_auto', $request->boolean('validation_auto') ? 'true' : 'false');

        $this->audit->log('modification_configuration');

        return back()->with('status', 'Configuration mise a jour.');
    }
}
