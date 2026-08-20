@extends('layouts.app')

@section('titre', 'Catalogue')

@section('contenu')
<div class="flex flex-col gap-6 sm:flex-row">
    <aside class="w-full shrink-0 sm:w-56">
        <div class="sticky top-4 space-y-4">
            <div>
                <label for="recherche" class="block text-sm font-medium text-bns-foreground">Rechercher</label>
                <input
                    id="recherche"
                    type="search"
                    placeholder="Titre, auteur, mot-cle..."
                    class="mt-1 block w-full rounded-md border border-bns-border px-3 py-2 text-sm shadow-sm focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring"
                >
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
        <h1 class="mb-4 font-heading text-2xl font-semibold text-bns-foreground">Catalogue</h1>

        <p id="catalogue-statut" class="mb-4 text-sm text-bns-muted-foreground" role="status" aria-live="polite"></p>

        <div id="catalogue-grille" class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4"></div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/catalogue.js'])
@endpush
