<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titre', 'Bibliotheque Numerique Scolaire')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bns-background font-sans text-bns-foreground antialiased">
    @auth
    <nav class="border-b border-bns-border bg-bns-card">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <div class="flex items-center gap-6">
                <span class="font-heading font-semibold text-bns-foreground">Bibliotheque Numerique Scolaire</span>
                @if (auth()->user()->isEleve())
                    <div class="hidden items-center gap-4 text-sm sm:flex">
                        <a href="{{ route('dashboard') }}" class="text-bns-muted-foreground hover:text-bns-foreground @if(request()->routeIs('dashboard')) font-medium text-bns-primary @endif">Mon espace</a>
                        <a href="{{ route('catalogue.index') }}" class="text-bns-muted-foreground hover:text-bns-foreground @if(request()->routeIs('catalogue.*')) font-medium text-bns-primary @endif">Catalogue</a>
                    </div>
                @endif
            </div>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-bns-muted-foreground">{{ auth()->user()->nomComplet() }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button variant="ghost" size="sm">Deconnexion</x-button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <main class="mx-auto max-w-6xl px-4 py-8">
        @if (session('status'))
            <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
        @endif

        @yield('contenu')
    </main>

    @stack('scripts')
</body>
</html>
