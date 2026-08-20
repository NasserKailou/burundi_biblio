<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Manuel;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\Parametre;
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
            ->where('uploader_id', $request->user()->id)
            ->with(['matiere', 'niveaux'])
            ->withCount('consultations')
            ->orderByDesc('created_at')
            ->get();

        return view('teacher.manuels.index', ['manuels' => $manuels]);
    }

    public function create(Request $request): View
    {
        return view('teacher.manuels.create', [
            'matieres' => Matiere::query()->orderBy('libelle')->get(),
            'niveaux' => Niveau::query()->whereIn('id', $request->user()->idsNiveauxGeres())->orderBy('ordre')->get(),
            'peutCommun' => $request->user()->peut_publier_commun,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $manuel = $this->enregistrer($request, new Manuel());

        $this->audit->log('upload_manuel', (string) $manuel->id);

        return redirect()->route('teacher.manuels.index')->with('status', 'Manuel enregistre avec succes.');
    }

    public function edit(Request $request, Manuel $manuel): View
    {
        $this->autoriserProprietaire($request, $manuel);

        $manuel->load('niveaux');

        return view('teacher.manuels.edit', [
            'manuel' => $manuel,
            'matieres' => Matiere::query()->orderBy('libelle')->get(),
            'niveaux' => Niveau::query()->whereIn('id', $request->user()->idsNiveauxGeres())->orderBy('ordre')->get(),
            'peutCommun' => $request->user()->peut_publier_commun,
        ]);
    }

    public function update(Request $request, Manuel $manuel): RedirectResponse
    {
        $this->autoriserProprietaire($request, $manuel);

        $this->enregistrer($request, $manuel);

        $this->audit->log('modification_manuel', (string) $manuel->id);

        return redirect()->route('teacher.manuels.index')->with('status', 'Manuel mis a jour avec succes.');
    }

    public function destroy(Request $request, Manuel $manuel): RedirectResponse
    {
        $this->autoriserProprietaire($request, $manuel);

        $this->files->supprimerFichiers($manuel);
        $manuel->delete();

        $this->audit->log('suppression_manuel', (string) $manuel->id);

        return redirect()->route('teacher.manuels.index')->with('status', 'Manuel supprime.');
    }

    private function autoriserProprietaire(Request $request, Manuel $manuel): void
    {
        abort_unless($manuel->uploader_id === $request->user()->id, 403);
    }

    private function enregistrer(Request $request, Manuel $manuel): Manuel
    {
        $user = $request->user();
        $niveauxAutorises = $user->idsNiveauxGeres();
        $tailleMaxKo = ((int) (Parametre::get('taille_max_fichier_mo') ?? 100)) * 1024;

        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'auteur' => ['nullable', 'string', 'max:255'],
            'annee' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'matiere_id' => ['required', 'exists:matieres,id'],
            'fichier' => [$manuel->exists ? 'nullable' : 'required', 'file', 'max:'.$tailleMaxKo],
            'couverture' => [$manuel->exists ? 'nullable' : 'required', 'image', 'max:5120'],
            'niveaux' => ['required', 'array', 'min:1'],
            'niveaux.*' => ['integer', Rule::in($niveauxAutorises)],
            'est_commun' => ['sometimes', 'boolean'],
            'statut' => ['required', Rule::in([Manuel::STATUT_PUBLIE, Manuel::STATUT_BROUILLON])],
        ]);

        $estCommun = $request->boolean('est_commun') && $user->peut_publier_commun;

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
            'est_commun' => $estCommun,
            'statut' => $data['statut'],
            'uploader_id' => $user->id,
        ]);

        if (isset($data['fichier'])) {
            $manuel->fichier = $data['fichier'];
            $manuel->type = $data['type'];
        }

        if (isset($data['couverture'])) {
            $manuel->couverture = $data['couverture'];
        }

        $manuel->save();
        $manuel->niveaux()->sync($data['niveaux']);

        return $manuel;
    }
}
