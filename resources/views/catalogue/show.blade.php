@extends('layouts.app')

@section('titre', $manuel->titre)

@section('contenu')
<a href="{{ route('catalogue.index') }}" class="mb-6 inline-block text-sm text-bns-muted-foreground hover:text-bns-foreground">
    &larr; Retour au catalogue
</a>

<div class="grid gap-8 sm:grid-cols-[240px_1fr]">
    <div>
        <x-book-cover :manuel="$manuel" />
    </div>

    <div>
        <h1 class="font-heading text-2xl font-semibold text-bns-foreground">{{ $manuel->titre }}</h1>

        <div class="mt-2 flex flex-wrap gap-2">
            <x-badge color="primary">{{ $manuel->matiere->libelle }}</x-badge>
            <x-badge>{{ strtoupper($manuel->type) }}</x-badge>
            @foreach ($manuel->niveaux as $niveau)
                <x-badge>{{ $niveau->libelle }}</x-badge>
            @endforeach
            @if ($manuel->est_commun)
                <x-badge color="accent">Commun a tous les niveaux</x-badge>
            @endif
        </div>

        <dl class="mt-4 space-y-1 text-sm text-bns-muted-foreground">
            @if ($manuel->auteur)
                <div><dt class="inline font-medium text-bns-foreground">Auteur : </dt><dd class="inline">{{ $manuel->auteur }}</dd></div>
            @endif
            @if ($manuel->annee)
                <div><dt class="inline font-medium text-bns-foreground">Annee : </dt><dd class="inline">{{ $manuel->annee }}</dd></div>
            @endif
        </dl>

        @if ($manuel->description)
            <p class="mt-4 text-sm leading-relaxed text-bns-foreground">{{ $manuel->description }}</p>
        @endif

        <div class="mt-6 flex items-center gap-3" id="actions-manuel" data-manuel-id="{{ $manuel->id }}" data-favori="{{ $estFavori ? '1' : '0' }}" data-favori-url="{{ route('api.favoris.store') }}" data-favori-destroy-url="{{ route('api.favoris.destroy', $manuel) }}">
            <a href="{{ route('reader.show', $manuel) }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-bns-primary px-4 py-2 text-sm font-medium text-bns-on-primary transition-colors duration-150 hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring focus-visible:ring-offset-2">
                Lire
            </a>
            <x-button id="btn-favori-fiche" variant="secondary" type="button" aria-pressed="{{ $estFavori ? 'true' : 'false' }}">
                {{ $estFavori ? 'Retirer des favoris' : 'Ajouter aux favoris' }}
            </x-button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/fiche-manuel.js'])
@endpush
