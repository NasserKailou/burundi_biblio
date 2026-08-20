@extends('layouts.admin')

@section('titre', 'Catalogue')

@section('admin-contenu')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-heading text-2xl font-semibold text-bns-foreground">Catalogue complet</h1>
    <a href="{{ route('admin.manuels.create') }}" class="inline-flex items-center rounded-md bg-bns-primary px-4 py-2 text-sm font-medium text-bns-on-primary hover:bg-teal-800">
        Ajouter un manuel
    </a>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Titre..." class="rounded-md border border-bns-border px-3 py-2 text-sm shadow-sm">
    <select name="matiere" class="rounded-md border border-bns-border bg-white px-3 py-2 text-sm shadow-sm">
        <option value="">Toutes les matieres</option>
        @foreach ($matieres as $matiere)
            <option value="{{ $matiere->id }}" @selected(request('matiere') == $matiere->id)>{{ $matiere->libelle }}</option>
        @endforeach
    </select>
    <select name="statut" class="rounded-md border border-bns-border bg-white px-3 py-2 text-sm shadow-sm">
        <option value="">Tous statuts</option>
        <option value="publie" @selected(request('statut') === 'publie')>Publie</option>
        <option value="brouillon" @selected(request('statut') === 'brouillon')>Brouillon</option>
    </select>
    <button type="submit" class="rounded-md border border-bns-border px-4 py-2 text-sm font-medium text-bns-foreground hover:bg-bns-muted">Filtrer</button>
</form>

<div class="overflow-x-auto rounded-xl border border-bns-border bg-bns-card">
    <table class="min-w-full divide-y divide-bns-border text-sm">
        <thead class="bg-bns-muted">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Titre</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Auteur / Enseignant</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Matiere</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Statut</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Consultations</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-bns-border">
            @foreach ($manuels as $manuel)
                <tr>
                    <td class="px-4 py-3 font-medium text-bns-foreground">
                        {{ $manuel->titre }}
                        @if ($manuel->est_commun)<x-badge color="accent" class="ml-2">Commun</x-badge>@endif
                    </td>
                    <td class="px-4 py-3 text-bns-muted-foreground">{{ $manuel->uploader?->nomComplet() }}</td>
                    <td class="px-4 py-3 text-bns-muted-foreground">{{ $manuel->matiere->libelle }}</td>
                    <td class="px-4 py-3">
                        <x-badge :color="$manuel->statut === 'publie' ? 'success' : 'muted'">{{ $manuel->statut === 'publie' ? 'Publie' : 'Brouillon' }}</x-badge>
                    </td>
                    <td class="px-4 py-3 text-bns-muted-foreground">{{ $manuel->consultations_count }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-right">
                        <a href="{{ route('admin.manuels.edit', $manuel) }}" class="mr-3 font-medium text-bns-primary hover:underline">Modifier</a>
                        <form method="POST" action="{{ route('admin.manuels.destroy', $manuel) }}" class="inline" data-confirm="Supprimer ce manuel ?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-bns-destructive hover:underline">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $manuels->links() }}</div>
@endsection
