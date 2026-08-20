<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NiveauController extends Controller
{
    public function __construct(private readonly AuditService $audit)
    {
    }

    public function index(): View
    {
        return view('admin.niveaux.index', [
            'niveaux' => Niveau::query()->withCount(['eleves', 'manuels'])->orderBy('ordre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'libelle' => ['required', 'string', 'max:100', Rule::unique('niveaux', 'libelle')],
            'ordre' => ['required', 'integer', 'min:0'],
        ]);

        $niveau = Niveau::query()->create($data);
        $this->audit->log('creation_niveau', (string) $niveau->id);

        return back()->with('status', 'Niveau cree.');
    }

    public function update(Request $request, Niveau $niveau): RedirectResponse
    {
        $data = $request->validate([
            'libelle' => ['required', 'string', 'max:100', Rule::unique('niveaux', 'libelle')->ignore($niveau->id)],
            'ordre' => ['required', 'integer', 'min:0'],
        ]);

        $niveau->update($data);
        $this->audit->log('modification_niveau', (string) $niveau->id);

        return back()->with('status', 'Niveau mis a jour.');
    }

    public function destroy(Niveau $niveau): RedirectResponse
    {
        abort_if($niveau->eleves()->exists() || $niveau->manuels()->exists(), 422, 'Ce niveau est utilise par des eleves ou des manuels, suppression impossible.');

        $id = $niveau->id;
        $niveau->delete();
        $this->audit->log('suppression_niveau', (string) $id);

        return back()->with('status', 'Niveau supprime.');
    }
}
