<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MatiereController extends Controller
{
    public function __construct(private readonly AuditService $audit)
    {
    }

    public function index(): View
    {
        return view('admin.matieres.index', [
            'matieres' => Matiere::query()->withCount('manuels')->orderBy('libelle')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'libelle' => ['required', 'string', 'max:100', Rule::unique('matieres', 'libelle')],
        ]);

        $matiere = Matiere::query()->create($data);
        $this->audit->log('creation_matiere', (string) $matiere->id);

        return back()->with('status', 'Matiere creee.');
    }

    public function update(Request $request, Matiere $matiere): RedirectResponse
    {
        $data = $request->validate([
            'libelle' => ['required', 'string', 'max:100', Rule::unique('matieres', 'libelle')->ignore($matiere->id)],
        ]);

        $matiere->update($data);
        $this->audit->log('modification_matiere', (string) $matiere->id);

        return back()->with('status', 'Matiere mise a jour.');
    }

    public function destroy(Matiere $matiere): RedirectResponse
    {
        abort_if($matiere->manuels()->exists(), 422, 'Cette matiere est utilisee par des manuels, suppression impossible.');

        $id = $matiere->id;
        $matiere->delete();
        $this->audit->log('suppression_matiere', (string) $id);

        return back()->with('status', 'Matiere supprimee.');
    }
}
