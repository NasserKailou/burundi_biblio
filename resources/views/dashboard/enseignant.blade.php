@extends('layouts.app')

@section('titre', 'Espace enseignant')

@section('contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Bonjour {{ $user->prenom }}</h1>

<div class="grid gap-6 sm:grid-cols-2">
    <x-card>
        <h2 class="mb-2 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Mes manuels</h2>
        <dl class="space-y-1 text-sm text-bns-foreground">
            <div class="flex justify-between"><dt>Total</dt><dd class="font-medium">{{ $nbManuels }}</dd></div>
            <div class="flex justify-between"><dt>Publies</dt><dd class="font-medium">{{ $nbPublies }}</dd></div>
        </dl>
    </x-card>

    <x-card>
        <h2 class="mb-2 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Niveaux geres</h2>
        <p class="text-sm text-bns-foreground">
            {{ $user->niveau?->libelle }}@if($user->niveauxEnseignes->isNotEmpty()), {{ $user->niveauxEnseignes->pluck('libelle')->join(', ') }}@endif
        </p>
    </x-card>
</div>
@endsection
