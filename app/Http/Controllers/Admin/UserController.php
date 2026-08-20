<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use App\Models\Parametre;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly AuditService $audit)
    {
    }

    public function index(Request $request): View
    {
        $utilisateurs = User::query()
            ->with(['role', 'niveau'])
            ->when($request->filled('role'), fn ($q) => $q->whereHas('role', fn ($r) => $r->where('libelle', $request->string('role'))))
            ->when($request->filled('statut'), fn ($q) => $q->where('actif', $request->string('statut') === 'actif'))
            ->when($request->filled('q'), function ($q) use ($request) {
                $terme = $request->string('q');
                $q->where(function ($q) use ($terme) {
                    $q->where('nom', 'like', "%{$terme}%")
                        ->orWhere('prenom', 'like', "%{$terme}%")
                        ->orWhere('identifiant', 'like', "%{$terme}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.utilisateurs.index', ['utilisateurs' => $utilisateurs]);
    }

    public function create(): View
    {
        return view('admin.utilisateurs.create', [
            'roles' => Role::query()->orderBy('libelle')->get(),
            'niveaux' => Niveau::query()->orderBy('ordre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validerDonnees($request, null);

        $user = User::query()->create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'identifiant' => $data['identifiant'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'niveau_id' => $data['niveau_id'] ?? null,
            'classe' => $data['classe'] ?? null,
            'actif' => $request->boolean('actif'),
            'peut_publier_commun' => $request->boolean('peut_publier_commun'),
        ]);

        $user->niveauxEnseignes()->sync($data['niveaux_geres'] ?? []);

        $this->audit->log('creation_utilisateur', (string) $user->id);

        return redirect()->route('admin.utilisateurs.index')->with('status', 'Utilisateur cree avec succes.');
    }

    public function edit(User $utilisateur): View
    {
        $utilisateur->load('niveauxEnseignes');

        return view('admin.utilisateurs.edit', [
            'utilisateur' => $utilisateur,
            'roles' => Role::query()->orderBy('libelle')->get(),
            'niveaux' => Niveau::query()->orderBy('ordre')->get(),
        ]);
    }

    public function update(Request $request, User $utilisateur): RedirectResponse
    {
        $data = $this->validerDonnees($request, $utilisateur);

        $utilisateur->fill([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'identifiant' => $data['identifiant'],
            'email' => $data['email'] ?? null,
            'role_id' => $data['role_id'],
            'niveau_id' => $data['niveau_id'] ?? null,
            'classe' => $data['classe'] ?? null,
            'actif' => $request->boolean('actif'),
            'peut_publier_commun' => $request->boolean('peut_publier_commun'),
        ]);

        if (! empty($data['password'])) {
            $utilisateur->password = Hash::make($data['password']);
        }

        $utilisateur->save();
        $utilisateur->niveauxEnseignes()->sync($data['niveaux_geres'] ?? []);

        $this->audit->log('modification_utilisateur', (string) $utilisateur->id);

        return redirect()->route('admin.utilisateurs.index')->with('status', 'Utilisateur mis a jour.');
    }

    public function destroy(User $utilisateur): RedirectResponse
    {
        $dernierAdmin = $utilisateur->role?->libelle === User::ROLE_ADMIN
            && User::query()->whereHas('role', fn ($r) => $r->where('libelle', User::ROLE_ADMIN))->count() <= 1;

        abort_if($dernierAdmin, 422, 'Impossible de supprimer le dernier administrateur.');

        $id = $utilisateur->id;
        $utilisateur->delete();

        $this->audit->log('suppression_utilisateur', (string) $id);

        return redirect()->route('admin.utilisateurs.index')->with('status', 'Utilisateur supprime.');
    }

    public function activer(User $utilisateur): RedirectResponse
    {
        $utilisateur->update(['actif' => true]);
        $this->audit->log('validation_inscription', (string) $utilisateur->id);

        return back()->with('status', "Compte de {$utilisateur->nomComplet()} valide.");
    }

    public function desactiver(User $utilisateur): RedirectResponse
    {
        $utilisateur->update(['actif' => false]);
        $this->audit->log('desactivation_utilisateur', (string) $utilisateur->id);

        return back()->with('status', "Compte de {$utilisateur->nomComplet()} desactive.");
    }

    public function reinitialiserMotDePasse(User $utilisateur): RedirectResponse
    {
        $motDePasse = Str::password(12);
        $utilisateur->update(['password' => Hash::make($motDePasse)]);

        $this->audit->log('reinitialisation_mot_de_passe', (string) $utilisateur->id);

        return back()->with('status', "Mot de passe reinitialise pour {$utilisateur->nomComplet()} : {$motDePasse} (a communiquer de maniere securisee, non stocke en clair).");
    }

    public function formulaireImport(): View
    {
        return view('admin.utilisateurs.import');
    }

    public function importer(Request $request): RedirectResponse
    {
        $request->validate(['csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);

        $roleEleve = Role::query()->where('libelle', 'eleve')->firstOrFail();
        $chemin = $request->file('csv')->getRealPath();
        $poignee = fopen($chemin, 'r');
        $entetes = fgetcsv($poignee, 0, ',');

        $cree = 0;
        $erreurs = [];
        $ligneNo = 1;

        while (($ligne = fgetcsv($poignee, 0, ',')) !== false) {
            $ligneNo++;
            $donnees = array_combine($entetes, $ligne);

            $niveau = Niveau::query()->where('libelle', trim($donnees['niveau'] ?? ''))->first();

            if (! $niveau || empty($donnees['nom']) || empty($donnees['prenom']) || empty($donnees['identifiant'])) {
                $erreurs[] = "Ligne {$ligneNo} : donnees manquantes ou niveau inconnu, ignoree.";
                continue;
            }

            if (User::query()->where('identifiant', $donnees['identifiant'])->exists()) {
                $erreurs[] = "Ligne {$ligneNo} : identifiant '{$donnees['identifiant']}' deja utilise, ignoree.";
                continue;
            }

            User::query()->create([
                'nom' => $donnees['nom'],
                'prenom' => $donnees['prenom'],
                'identifiant' => $donnees['identifiant'],
                'password' => Hash::make(Str::password(12)),
                'role_id' => $roleEleve->id,
                'niveau_id' => $niveau->id,
                'classe' => $donnees['classe'] ?? null,
                'actif' => true,
            ]);

            $cree++;
        }

        fclose($poignee);

        $this->audit->log('import_csv_utilisateurs', "{$cree} utilisateur(s)");

        return redirect()->route('admin.utilisateurs.index')
            ->with('status', "{$cree} utilisateur(s) importe(s).")
            ->with('erreurs_import', $erreurs);
    }

    private function validerDonnees(Request $request, ?User $utilisateur): array
    {
        $longueurMin = (int) (Parametre::get('politique_mdp_longueur_min') ?? 8);
        $estCreation = $utilisateur === null;

        return $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'identifiant' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'identifiant')->ignore($utilisateur?->id)],
            'email' => ['nullable', 'email', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'niveau_id' => ['nullable', 'exists:niveaux,id'],
            'classe' => ['nullable', 'string', 'max:50'],
            'niveaux_geres' => ['nullable', 'array'],
            'niveaux_geres.*' => ['integer', 'exists:niveaux,id'],
            'password' => [$estCreation ? 'required' : 'nullable', Password::min($longueurMin)->mixedCase()->numbers()],
        ]);
    }
}
