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
    </div>
</div>
@endsection
