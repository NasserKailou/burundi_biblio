@extends('layouts.app')

@section('titre', 'Mon espace')

@section('contenu')
<h1 class="mb-6 text-2xl font-semibold text-slate-900">Bonjour {{ $user->prenom }}</h1>

<div class="grid gap-6 sm:grid-cols-2">
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-2 text-sm font-medium uppercase tracking-wide text-slate-500">Mon profil</h2>
        <dl class="space-y-1 text-sm text-slate-700">
            <div class="flex justify-between"><dt>Niveau</dt><dd class="font-medium">{{ $user->niveau?->libelle ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt>Classe</dt><dd class="font-medium">{{ $user->classe ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt>Identifiant</dt><dd class="font-medium">{{ $user->identifiant }}</dd></div>
        </dl>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-2 text-sm font-medium uppercase tracking-wide text-slate-500">Activite</h2>
        <p class="text-sm text-slate-700">{{ $user->consultations()->count() }} lecture(s) enregistree(s).</p>
        <p class="text-sm text-slate-700">{{ $user->favoris()->count() }} favori(s).</p>
    </div>
</div>
@endsection
