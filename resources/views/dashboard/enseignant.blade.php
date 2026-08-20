@extends('layouts.app')

@section('titre', 'Espace enseignant')

@section('contenu')
<x-page-header title="Bonjour {{ $user->prenom }}" description="Vue d'ensemble de vos publications sur la plateforme." icon="home" />

<div class="grid gap-6 sm:grid-cols-3">
    <x-stat-card label="Manuels" :value="$nbManuels" icon="book-open" accent="primary" />
    <x-stat-card label="Publies" :value="$nbPublies" icon="check-circle" accent="success" />
    <x-card>
        <h2 class="mb-2 flex items-center gap-2 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">
            <x-icon name="layers" class="h-4 w-4 text-bns-primary" /> Niveaux geres
        </h2>
        <p class="text-sm text-bns-foreground">
            {{ $user->niveau?->libelle }}@if($user->niveauxEnseignes->isNotEmpty()), {{ $user->niveauxEnseignes->pluck('libelle')->join(', ') }}@endif
        </p>
    </x-card>
</div>
@endsection
