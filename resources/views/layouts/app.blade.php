<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titre', 'Bibliotheque Numerique Scolaire')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    @auth
    <nav class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <span class="font-semibold text-slate-800">Bibliotheque Numerique Scolaire</span>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-slate-600">{{ auth()->user()->nomComplet() }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-500 hover:text-slate-800">Deconnexion</button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <main class="mx-auto max-w-6xl px-4 py-8">
        @if (session('status'))
            <div class="mb-6 rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @yield('contenu')
    </main>
</body>
</html>
