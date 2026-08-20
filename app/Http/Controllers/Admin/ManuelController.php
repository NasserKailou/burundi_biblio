<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manuel;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\Parametre;
use App\Models\User;
use App\Services\AuditService;
use App\Services\FileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ManuelController extends Controller
{
    public function __construct(
        private readonly FileService $files,
        private readonly AuditService $audit,
    ) {
    }

    public function index(Request $request): View
    {
        $manuels = Manuel::query()
            ->with(['matiere', 'niveaux', 'uploader'])
            ->withCount('consultations')
            ->when($request->filled('matiere'), fn ($q) => $q->where('matiere_id', $request->integer('matiere')))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->string('statut')))
            ->when($request->filled('q'), fn ($q) => $q->where('titre', 'like', '%'.$request->string('q').'%'))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.manuels.index', [
            'manuels' => $manuels,
            'matieres' => Matiere::query()->orderBy('libelle')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.manuels.create', [
            'matieres' => Matiere::query()->orderBy('libelle')->get(),
            'niveaux' => Niveau::query()->orderBy('ordre')->get(),
            'enseignants' => User::query()->whereHas('role', fn ($r) => $r->whereIn('libelle', ['enseignant', 'admin']))->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $manuel = $this->enregistrer($request, new Manuel());

        $this->audit->log('upload_manuel_admin', (string) $manuel->id);

        return redirect()->route('admin.manuels.index')->with('status', 'Manuel enregistre avec succes.');
    }

    public function edit(Manuel $manuel): View
    {
        $manuel->load('niveaux');

        return view('admin.manuels.edit', [
            'manuel' => $manuel,
            'matieres' => Matiere::query()->orderBy('libelle')->get(),
            'niveaux' => Niveau::query()->orderBy('ordre')->get(),
            'enseignants' => User::query()->whereHas('role', fn ($r) => $r->whereIn('libelle', ['enseignant', 'admin']))->orderBy('nom')->get(),
        ]);
    }

    public function update(Request $request, Manuel $manuel): RedirectResponse
    {
        $this->enregistrer($request, $manuel);

        $this->audit->log('modification_manuel_admin', (string) $manuel->id);

        return redirect()->route('admin.manuels.index')->with('status', 'Manuel mis a jour.');
    }

    public function destroy(Manuel $manuel): RedirectResponse
    {
        $this->files->supprimerFichiers($manuel);
        $id = $manuel->id;
        $manuel->delete();

        $this->audit->log('suppression_manuel_admin', (string) $id);

        return redirect()->route('admin.manuels.index')->with('status', 'Manuel supprime.');
    }

    private function enregistrer(Request $request, Manuel $manuel): Manuel
    {
        $tailleMaxKo = ((int) (Parametre::get('taille_max_fichier_mo') ?? 100)) * 1024;

        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'auteur' => ['nullable', 'string', 'max:255'],
            'annee' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'matiere_id' => ['required', 'exists:matieres,id'],
            'uploader_id' => ['required', 'exists:users,id'],
            'fichier' => [$manuel->exists ? 'nullable' : 'required', 'file', 'max:'.$tailleMaxKo],
            'couverture' => [$manuel->exists ? 'nullable' : 'required', 'image', 'max:5120'],
            'niveaux' => ['nullable', 'array'],
            'niveaux.*' => ['integer', 'exists:niveaux,id'],
            'est_commun' => ['sometimes', 'boolean'],
            'statut' => ['required', Rule::in([Manuel::STATUT_PUBLIE, Manuel::STATUT_BROUILLON])],
        ]);

        if ($request->hasFile('fichier')) {
            $type = $this->files->detecterType($request->file('fichier'));
            abort_if($type === null, 422, "Le fichier fourni n'est ni un PDF ni un EPUB valide.");

            if ($manuel->exists) {
                $this->files->supprimerFichierManuel($manuel);
            }

            $data['fichier'] = $this->files->storerManuel($request->file('fichier'), $type);
            $data['type'] = $type;
        }

        if ($request->hasFile('couverture')) {
            if ($manuel->exists) {
                $this->files->supprimerCouverture($manuel);
            }

            $data['couverture'] = $this->files->storerCouverture($request->file('couverture'));
        }

        $manuel->fill([
            'titre' => $data['titre'],
            'description' => $data['description'] ?? null,
            'auteur' => $data['auteur'] ?? null,
            'annee' => $data['annee'] ?? null,
            'matiere_id' => $data['matiere_id'],
            'uploader_id' => $data['uploader_id'],
            'est_commun' => $request->boolean('est_commun'),
            'statut' => $data['statut'],
        ]);

        if (isset($data['fichier'])) {
            $manuel->fichier = $data['fichier'];
            $manuel->type = $data['type'];
        }

        if (isset($data['couverture'])) {
            $manuel->couverture = $data['couverture'];
        }

        $manuel->save();
        $manuel->niveaux()->sync($data['niveaux'] ?? []);

        return $manuel;
    }
}
