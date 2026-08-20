@extends('layouts.app')

@section('titre', 'Administration')

@section('contenu')
<h1 class="mb-6 text-2xl font-semibold text-slate-900">Tableau de bord administrateur</h1>

<div class="grid gap-6 sm:grid-cols-3">
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium uppercase tracking-wide text-slate-500">Utilisateurs</p>
        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $nbUtilisateurs }}</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium uppercase tracking-wide text-slate-500">En attente de validation</p>
        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $nbUtilisateursEnAttente }}</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium uppercase tracking-wide text-slate-500">Manuels</p>
        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $nbManuels }}</p>
    </div>
</div>
@endsection
