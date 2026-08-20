@extends('layouts.app')

@section('titre', 'Catalogue')

@section('contenu')
<x-page-header title="Catalogue" description="Parcourez les manuels disponibles pour votre niveau." icon="book-open" />

<div class="flex flex-col gap-6 sm:flex-row">
    <aside class="w-full shrink-0 sm:w-60">
        <div class="sticky top-24 space-y-4 rounded-xl border border-bns-border bg-bns-card p-4 shadow-sm">
            <div>
                <label for="recherche" class="block text-sm font-medium text-bns-foreground">Rechercher</label>
                <div class="relative mt-1">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bns-muted-foreground" />
                    <input
                        id="recherche"
                        type="search"
                        placeholder="Titre, auteur, mot-cle..."
                        class="block w-full rounded-md border border-bns-border py-2 pl-9 pr-3 text-sm shadow-sm transition-colors focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring"
                    >
                </div>
            </div>
            <div>
                <label for="filtre-matiere" class="block text-sm font-medium text-bns-foreground">Matiere</label>
                <select
                    id="filtre-matiere"
                    class="mt-1 block w-full rounded-md border border-bns-border bg-white px-3 py-2 text-sm shadow-sm focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring"
                >
                    <option value="">Toutes les matieres</option>
                    @foreach ($matieres as $matiere)
                        <option value="{{ $matiere->id }}">{{ $matiere->libelle }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <p id="catalogue-statut" class="mb-4 text-sm text-bns-muted-foreground" role="status" aria-live="polite"></p>

        <div id="catalogue-grille" class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4"></div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/catalogue.js'])
@endpush
