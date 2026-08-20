@extends('layouts.admin')

@section('titre', 'Catalogue')

@section('admin-contenu')
<x-page-header title="Catalogue complet" description="Gerez l'ensemble des manuels disponibles sur la plateforme." icon="book-open">
    <x-slot:actions>
        <x-button variant="primary" href="{{ route('admin.manuels.create') }}">
            <x-icon name="plus" class="h-4 w-4" /> Ajouter un manuel
        </x-button>
    </x-slot:actions>
</x-page-header>

<form method="GET" class="mb-5 flex flex-wrap items-center gap-3 rounded-xl border border-bns-border bg-bns-card p-3 shadow-sm">
    <div class="relative min-w-[200px] flex-1">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bns-muted-foreground" />
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un titre..." class="w-full rounded-md border border-bns-border py-2 pl-9 pr-3 text-sm shadow-sm transition-colors focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring">
    </div>
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
    <button type="submit" class="inline-flex items-center gap-2 rounded-md border border-bns-border px-4 py-2 text-sm font-medium text-bns-foreground transition-colors hover:bg-bns-muted">
        <x-icon name="filter" class="h-4 w-4" /> Filtrer
    </button>
</form>

<div class="overflow-hidden rounded-xl border border-bns-border bg-bns-card shadow-sm">
    @if ($manuels->isEmpty())
        <x-empty-state icon="book-open" title="Aucun manuel ne correspond a ces criteres" description="Ajustez vos filtres ou ajoutez un nouveau manuel au catalogue.">
            <x-slot:action>
                <x-button variant="secondary" href="{{ route('admin.manuels.create') }}">
                    <x-icon name="plus" class="h-4 w-4" /> Ajouter un manuel
                </x-button>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-bns-border text-sm">
                <thead class="bg-bns-muted/70">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Titre</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Auteur / Enseignant</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Matiere</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Statut</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Consultations</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bns-border">
                    @foreach ($manuels as $manuel)
                        <tr class="transition-colors hover:bg-bns-muted/40">
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
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.manuels.edit', $manuel) }}" title="Modifier" class="flex h-8 w-8 items-center justify-center rounded-md text-bns-primary transition-colors hover:bg-teal-600/10">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.manuels.destroy', $manuel) }}" data-confirm="Supprimer ce manuel ?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Supprimer" class="flex h-8 w-8 items-center justify-center rounded-md text-bns-destructive transition-colors hover:bg-red-50">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="mt-4">{{ $manuels->links() }}</div>
@endsection
