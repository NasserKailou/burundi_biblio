@extends('layouts.app')

@section('titre', 'Mon espace')

@section('contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Bonjour {{ $user->prenom }}</h1>

<div class="grid gap-6 sm:grid-cols-2">
    <x-card>
        <h2 class="mb-2 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Mon profil</h2>
        <dl class="space-y-1 text-sm text-bns-foreground">
            <div class="flex justify-between"><dt>Niveau</dt><dd class="font-medium">{{ $user->niveau?->libelle ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt>Classe</dt><dd class="font-medium">{{ $user->classe ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt>Identifiant</dt><dd class="font-medium">{{ $user->identifiant }}</dd></div>
        </dl>
    </x-card>

    <x-card>
        <h2 class="mb-2 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Activite</h2>
        <p class="text-sm text-bns-foreground">{{ $user->consultations()->count() }} lecture(s) enregistree(s).</p>
        <p class="text-sm text-bns-foreground">{{ $user->favoris()->count() }} favori(s).</p>
    </x-card>
</div>
@endsection
