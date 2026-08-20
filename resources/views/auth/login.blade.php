@extends('layouts.guest')

@section('titre', 'Connexion')

@section('contenu')
<div class="mb-8 lg:hidden">
    <div class="flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-bns-primary font-heading text-sm font-bold text-white">BNS</span>
        <span class="font-heading text-base font-semibold text-bns-foreground">Bibliothèque Numérique Scolaire</span>
    </div>
</div>

<h1 class="font-heading text-2xl font-semibold text-bns-foreground">Bon retour</h1>
<p class="mb-8 mt-1 text-sm text-bns-muted-foreground">Connectez-vous pour accéder à votre bibliothèque.</p>

@if ($errors->any())
    <x-alert type="error" class="mb-6">
        <ul class="list-inside list-disc">
            @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif

<form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
    @csrf
    <x-input name="identifiant" label="Identifiant" required autofocus value="{{ old('identifiant') }}" />
    <x-input name="password" type="password" label="Mot de passe" required />

    <label class="flex items-center gap-2 text-sm text-bns-muted-foreground">
        <input type="checkbox" name="remember" class="rounded border-bns-border text-bns-primary focus:ring-bns-ring">
        Se souvenir de moi
    </label>

    <x-button variant="primary" class="w-full !py-2.5 shadow-sm shadow-teal-900/10 transition-transform hover:-translate-y-0.5">
        Se connecter
    </x-button>
</form>

<p class="mt-8 text-center text-sm text-bns-muted-foreground">
    Pas encore de compte élève ?
    <a href="{{ route('register') }}" class="font-medium text-bns-primary hover:underline">Créer un compte</a>
</p>
@endsection
