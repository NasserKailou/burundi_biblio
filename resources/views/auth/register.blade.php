@extends('layouts.app')

@section('titre', 'Inscription')

@section('contenu')
<div class="mx-auto mt-12 max-w-md">
    <x-card>
        <h1 class="mb-1 font-heading text-xl font-semibold text-bns-foreground">Creer un compte eleve</h1>
        <p class="mb-6 text-sm text-bns-muted-foreground">Votre compte devra etre valide par un administrateur avant la premiere connexion, sauf si la validation automatique est activee.</p>

        @if ($errors->any())
            <x-alert type="error" class="mb-4">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form method="POST" action="{{ route('register.attempt') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <x-input name="prenom" label="Prenom" required value="{{ old('prenom') }}" />
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

            <x-button variant="primary" class="w-full">Creer mon compte</x-button>
        </form>

        <p class="mt-6 text-center text-sm text-bns-muted-foreground">
            Deja un compte ?
            <a href="{{ route('login') }}" class="font-medium text-bns-primary hover:underline">Se connecter</a>
        </p>
    </x-card>
</div>
@endsection
