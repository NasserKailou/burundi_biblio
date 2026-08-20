@extends('layouts.admin')

@section('titre', 'Administration')

@section('admin-contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Tableau de bord administrateur</h1>

<div class="grid gap-6 sm:grid-cols-3">
    <x-stat-card label="Utilisateurs" :value="$nbUtilisateurs" />
    <x-stat-card label="En attente de validation" :value="$nbUtilisateursEnAttente" />
    <x-stat-card label="Manuels" :value="$nbManuels" />
</div>
@endsection
