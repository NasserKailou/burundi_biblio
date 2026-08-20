@extends('layouts.admin')

@section('titre', 'Administration')

@section('admin-contenu')
<x-page-header title="Tableau de bord administrateur" description="Vue d'ensemble de l'activite de la bibliotheque numerique." icon="grid" />

<div class="grid gap-6 sm:grid-cols-3">
    <x-stat-card label="Utilisateurs" :value="$nbUtilisateurs" icon="users" accent="primary" />
    <x-stat-card label="En attente de validation" :value="$nbUtilisateursEnAttente" icon="clock" accent="accent" />
    <x-stat-card label="Manuels" :value="$nbManuels" icon="book-open" accent="success" />
</div>

<div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <a href="{{ route('admin.utilisateurs.index') }}" class="group flex items-center gap-3 rounded-xl border border-bns-border bg-bns-card p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:border-bns-primary/40 hover:shadow-md">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-600/10 text-bns-primary"><x-icon name="users" class="h-5 w-5" /></span>
        <div>
            <p class="text-sm font-semibold text-bns-foreground">Utilisateurs</p>
            <p class="text-xs text-bns-muted-foreground">Gerer les comptes</p>
        </div>
    </a>
    <a href="{{ route('admin.manuels.create') }}" class="group flex items-center gap-3 rounded-xl border border-bns-border bg-bns-card p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:border-bns-primary/40 hover:shadow-md">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-600/10 text-bns-primary"><x-icon name="plus" class="h-5 w-5" /></span>
        <div>
            <p class="text-sm font-semibold text-bns-foreground">Ajouter un manuel</p>
            <p class="text-xs text-bns-muted-foreground">Publier une ressource</p>
        </div>
    </a>
    <a href="{{ route('admin.statistiques.index') }}" class="group flex items-center gap-3 rounded-xl border border-bns-border bg-bns-card p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:border-bns-primary/40 hover:shadow-md">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-600/10 text-bns-primary"><x-icon name="chart-bar" class="h-5 w-5" /></span>
        <div>
            <p class="text-sm font-semibold text-bns-foreground">Statistiques</p>
            <p class="text-xs text-bns-muted-foreground">Suivre l'usage</p>
        </div>
    </a>
    <a href="{{ route('admin.audit.index') }}" class="group flex items-center gap-3 rounded-xl border border-bns-border bg-bns-card p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:border-bns-primary/40 hover:shadow-md">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-600/10 text-bns-primary"><x-icon name="shield-check" class="h-5 w-5" /></span>
        <div>
            <p class="text-sm font-semibold text-bns-foreground">Journaux d'audit</p>
            <p class="text-xs text-bns-muted-foreground">Controler la securite</p>
        </div>
    </a>
</div>
@endsection
