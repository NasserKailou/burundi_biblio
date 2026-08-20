@extends('layouts.app')

@section('titre', 'Connexion')

@section('contenu')
<div class="mx-auto mt-12 max-w-md">
    <div class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="mb-1 text-xl font-semibold text-slate-900">Connexion</h1>
        <p class="mb-6 text-sm text-slate-500">Bibliotheque Numerique Scolaire</p>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf
            <div>
                <label for="identifiant" class="block text-sm font-medium text-slate-700">Identifiant</label>
                <input id="identifiant" name="identifiant" type="text" required autofocus
                    value="{{ old('identifiant') }}"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Mot de passe</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                Se souvenir de moi
            </label>
            <button type="submit"
                class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Se connecter
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Pas encore de compte eleve ?
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:underline">Creer un compte</a>
        </p>
    </div>
</div>
@endsection
