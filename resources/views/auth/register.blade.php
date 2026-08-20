@extends('layouts.app')

@section('titre', 'Inscription')

@section('contenu')
<div class="mx-auto mt-12 max-w-md">
    <div class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="mb-1 text-xl font-semibold text-slate-900">Creer un compte eleve</h1>
        <p class="mb-6 text-sm text-slate-500">Votre compte devra etre valide par un administrateur avant la premiere connexion, sauf si la validation automatique est activee.</p>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.attempt') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="prenom" class="block text-sm font-medium text-slate-700">Prenom</label>
                    <input id="prenom" name="prenom" type="text" required value="{{ old('prenom') }}"
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="nom" class="block text-sm font-medium text-slate-700">Nom</label>
                    <input id="nom" name="nom" type="text" required value="{{ old('nom') }}"
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label for="identifiant" class="block text-sm font-medium text-slate-700">Identifiant</label>
                <input id="identifiant" name="identifiant" type="text" required value="{{ old('identifiant') }}"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="niveau_id" class="block text-sm font-medium text-slate-700">Niveau</label>
                    <select id="niveau_id" name="niveau_id" required
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Choisir --</option>
                        @foreach ($niveaux as $niveau)
                            <option value="{{ $niveau->id }}" @selected(old('niveau_id') == $niveau->id)>{{ $niveau->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="classe" class="block text-sm font-medium text-slate-700">Classe (optionnel)</label>
                    <input id="classe" name="classe" type="text" value="{{ old('classe') }}"
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Mot de passe</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirmer le mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <button type="submit"
                class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Creer mon compte
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Deja un compte ?
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:underline">Se connecter</a>
        </p>
    </div>
</div>
@endsection
