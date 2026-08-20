@extends('layouts.app')

@section('titre', 'Espace enseignant')

@section('contenu')
<h1 class="mb-6 text-2xl font-semibold text-slate-900">Bonjour {{ $user->prenom }}</h1>

<div class="grid gap-6 sm:grid-cols-2">
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-2 text-sm font-medium uppercase tracking-wide text-slate-500">Mes manuels</h2>
        <dl class="space-y-1 text-sm text-slate-700">
            <div class="flex justify-between"><dt>Total</dt><dd class="font-medium">{{ $nbManuels }}</dd></div>
            <div class="flex justify-between"><dt>Publies</dt><dd class="font-medium">{{ $nbPublies }}</dd></div>
        </dl>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-2 text-sm font-medium uppercase tracking-wide text-slate-500">Niveaux geres</h2>
        <p class="text-sm text-slate-700">
            {{ $user->niveau?->libelle }}@if($user->niveauxEnseignes->isNotEmpty()), {{ $user->niveauxEnseignes->pluck('libelle')->join(', ') }}@endif
        </p>
    </div>
</div>
@endsection
