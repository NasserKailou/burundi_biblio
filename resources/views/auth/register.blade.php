@extends('layouts.guest')

@section('titre', 'Inscription')

@section('contenu')
<div class="mb-8 lg:hidden">
    <div class="flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-bns-primary font-heading text-sm font-bold text-white">BNS</span>
        <span class="font-heading text-base font-semibold text-bns-foreground">Bibliothèque Numérique Scolaire</span>
    </div>
</div>

<h1 class="font-heading text-2xl font-semibold text-bns-foreground">Créer un compte élève</h1>
<p class="mb-8 mt-1 text-sm text-bns-muted-foreground">
    Votre compte devra être validé par un administrateur avant votre première connexion, sauf si la validation automatique est activée.
</p>

@if ($errors->any())
    <x-alert type="error" class="mb-6">
        <ul class="list-inside list-disc">
            @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif

<form method="POST" action="{{ route('register.attempt') }}" class="space-y-5">
    @csrf
    <div class="grid grid-cols-2 gap-4">
        <x-input name="prenom" label="Prénom" required value="{{ old('prenom') }}" />
        <x-input name="nom" label="Nom" required value="{{ old('nom') }}" />
    </div>

    <x-input name="identifiant" label="Identifiant" required value="{{ old('identifiant') }}" />

    <div class="grid grid-cols-2 gap-4">
        <x-select name="niveau_id" label="Niveau" required>
            <option value="">-- Choisir --</option>
            @foreach ($niveaux as $niveau)
                <option value="{{ $niveau->id }}" @selected(old('niveau_id') == $niveau->id)>{{ $niveau->libelle }}</option>
            @endforeach
        </x-select>
        <x-input name="classe" label="Classe (optionnel)" value="{{ old('classe') }}" />
    </div>

    <x-input name="password" type="password" label="Mot de passe" required />
    <x-input name="password_confirmation" type="password" label="Confirmer le mot de passe" required />

    <x-button variant="primary" class="w-full !py-2.5 shadow-sm shadow-teal-900/10 transition-transform hover:-translate-y-0.5">
        Créer mon compte
    </x-button>
</form>

<p class="mt-8 text-center text-sm text-bns-muted-foreground">
    Déjà un compte ?
    <a href="{{ route('login') }}" class="font-medium text-bns-primary hover:underline">Se connecter</a>
</p>
@endsection
