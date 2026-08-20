@extends('layouts.app')

@section('titre', 'Connexion')

@section('contenu')
<div class="mx-auto mt-12 max-w-md">
    <x-card>
        <h1 class="mb-1 font-heading text-xl font-semibold text-bns-foreground">Connexion</h1>
        <p class="mb-6 text-sm text-bns-muted-foreground">Bibliotheque Numerique Scolaire</p>

        @if ($errors->any())
            <x-alert type="error" class="mb-4">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf
            <x-input name="identifiant" label="Identifiant" required autofocus value="{{ old('identifiant') }}" />
            <x-input name="password" type="password" label="Mot de passe" required />

            <label class="flex items-center gap-2 text-sm text-bns-muted-foreground">
                <input type="checkbox" name="remember" class="rounded border-bns-border text-bns-primary focus:ring-bns-ring">
                Se souvenir de moi
            </label>

            <x-button variant="primary" class="w-full">Se connecter</x-button>
        </form>

        <p class="mt-6 text-center text-sm text-bns-muted-foreground">
            Pas encore de compte eleve ?
            <a href="{{ route('register') }}" class="font-medium text-bns-primary hover:underline">Creer un compte</a>
        </p>
    </x-card>
</div>
@endsection
