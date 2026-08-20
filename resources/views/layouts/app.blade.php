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
    @auth
    @php
    $roleLabel = auth()->user()->isAdmin() ? 'Administrateur' : (auth()->user()->isEnseignant() ? 'Enseignant' : 'Eleve');
    $initiales = strtoupper(mb_substr(auth()->user()->prenom, 0, 1) . mb_substr(auth()->user()->nom, 0, 1));
    @endphp
    <nav class="sticky top-0 z-20 border-b border-bns-border bg-bns-card/95 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
            <div class="flex items-center gap-8">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isEnseignant() ? route('teacher.dashboard') : route('dashboard')) }}" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-bns-primary font-heading text-xs font-bold text-white">BNS</span>
                    <span class="hidden font-heading text-[15px] font-semibold text-bns-foreground sm:inline">Bibliotheque Numerique Scolaire</span>
                </a>
                @if (auth()->user()->isEleve())
                    <div class="hidden items-center gap-1 text-sm sm:flex">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 transition-colors @if(request()->routeIs('dashboard')) bg-teal-600/10 font-medium text-bns-primary @else text-bns-muted-foreground hover:bg-bns-muted hover:text-bns-foreground @endif">
                            <x-icon name="home" class="h-4 w-4" /> Mon espace
                        </a>
                        <a href="{{ route('catalogue.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 transition-colors @if(request()->routeIs('catalogue.*')) bg-teal-600/10 font-medium text-bns-primary @else text-bns-muted-foreground hover:bg-bns-muted hover:text-bns-foreground @endif">
                            <x-icon name="book-open" class="h-4 w-4" /> Catalogue
                        </a>
                    </div>
                @elseif (auth()->user()->isEnseignant())
                    <div class="hidden items-center gap-1 text-sm sm:flex">
                        <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 transition-colors @if(request()->routeIs('teacher.dashboard')) bg-teal-600/10 font-medium text-bns-primary @else text-bns-muted-foreground hover:bg-bns-muted hover:text-bns-foreground @endif">
                            <x-icon name="grid" class="h-4 w-4" /> Tableau de bord
                        </a>
                        <a href="{{ route('teacher.manuels.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 transition-colors @if(request()->routeIs('teacher.manuels.*')) bg-teal-600/10 font-medium text-bns-primary @else text-bns-muted-foreground hover:bg-bns-muted hover:text-bns-foreground @endif">
                            <x-icon name="book-open" class="h-4 w-4" /> Mes manuels
                        </a>
                        <a href="{{ route('teacher.statistiques.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 transition-colors @if(request()->routeIs('teacher.statistiques.*')) bg-teal-600/10 font-medium text-bns-primary @else text-bns-muted-foreground hover:bg-bns-muted hover:text-bns-foreground @endif">
                            <x-icon name="chart-bar" class="h-4 w-4" /> Statistiques
                        </a>
                    </div>
                @endif
            </div>
            <div class="flex items-center gap-3 text-sm">
                <div class="hidden items-center gap-2.5 rounded-full border border-bns-border py-1 pl-1 pr-3 sm:flex">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-teal-600/10 text-xs font-semibold text-bns-primary">{{ $initiales }}</span>
                    <span class="leading-tight">
                        <span class="block text-sm font-medium text-bns-foreground">{{ auth()->user()->nomComplet() }}</span>
                        <span class="block text-[11px] text-bns-muted-foreground">{{ $roleLabel }}</span>
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button variant="ghost" size="sm" title="Deconnexion" aria-label="Deconnexion">
                        <x-icon name="logout" class="h-4 w-4" />
                        <span class="hidden md:inline">Deconnexion</span>
                    </x-button>
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
