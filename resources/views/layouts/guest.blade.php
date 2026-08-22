<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'Bibliotheque Numerique Scolaire')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bns-background font-sans text-bns-foreground antialiased">
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-gradient-to-br from-sky-800 via-sky-700 to-sky-900 lg:flex lg:flex-col lg:justify-between lg:p-12">
            <div class="bns-blob bns-blob-1" aria-hidden="true"></div>
            <div class="bns-blob bns-blob-2" aria-hidden="true"></div>

            <a href="{{ route('accueil') }}" class="relative z-10 flex items-center gap-3 rounded-lg transition-opacity hover:opacity-80 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 font-heading text-lg font-bold text-white ring-1 ring-white/25">BNS</span>
                <span class="font-heading text-lg font-semibold text-white">Bibliothèque Numérique Scolaire</span>
            </a>

            <div class="relative z-10 max-w-md">
                <h1 class="font-heading text-3xl font-semibold leading-tight text-white">
                    Tous les manuels de l'établissement, réunis au même endroit.
                </h1>
                <p class="mt-4 text-sky-100">
                    Un accès simple et sécurisé aux ressources pédagogiques de votre niveau,
                    disponible sur le réseau de l'établissement.
                </p>
            </div>

            <p class="relative z-10 text-sm text-sky-200">© {{ date('Y') }} — Usage interne à l'établissement.</p>
        </div>

        <div class="flex items-center justify-center px-4 py-12 sm:px-8">
            <div class="bns-fade-in-up w-full max-w-md">
                <a href="{{ route('accueil') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-bns-muted-foreground hover:text-bns-primary lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    Retour au site
                </a>

                @if (session('status'))
                    <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
                @endif

                @yield('contenu')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
