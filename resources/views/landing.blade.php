<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bibliotheque Numerique Scolaire</title>
    <meta name="description" content="La bibliotheque numerique de l'etablissement : catalogue de manuels, lecture PDF/EPUB et suivi pedagogique, reserves aux eleves, enseignants et personnel.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bns-background font-sans text-bns-foreground antialiased">

    <header class="sticky top-0 z-20 border-b border-bns-border bg-bns-card/95 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-bns-primary font-heading text-xs font-bold text-white">BNS</span>
                <span class="font-heading text-[15px] font-semibold text-bns-foreground">Bibliotheque Numerique Scolaire</span>
            </a>
            <nav class="hidden items-center gap-6 text-sm font-medium text-bns-muted-foreground sm:flex">
                <a href="#fonctionnalites" class="hover:text-bns-foreground">Fonctionnalites</a>
                <a href="#comment-ca-marche" class="hover:text-bns-foreground">Comment ca marche</a>
            </nav>
            <div class="flex items-center gap-2">
                <x-button variant="ghost" size="sm" href="{{ route('login') }}">Se connecter</x-button>
                <x-button variant="primary" size="sm" href="{{ route('register') }}">Creer un compte</x-button>
            </div>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden bg-gradient-to-br from-teal-800 via-teal-700 to-teal-900">
            <div class="bns-blob bns-blob-1" aria-hidden="true"></div>
            <div class="bns-blob bns-blob-2" aria-hidden="true"></div>

            <div class="relative z-10 mx-auto grid max-w-6xl gap-12 px-4 py-20 lg:grid-cols-2 lg:items-center lg:py-28">
                <div class="bns-fade-in-up">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-teal-100 ring-1 ring-white/20">
                        <x-icon name="sparkles" class="h-3.5 w-3.5" /> Plateforme interne de l'etablissement
                    </span>
                    <h1 class="mt-5 font-heading text-4xl font-semibold leading-tight text-white sm:text-5xl">
                        Tous les manuels de l'etablissement, reunis au meme endroit.
                    </h1>
                    <p class="mt-5 max-w-lg text-base text-teal-100">
                        Un espace numerique simple et securise pour consulter, organiser et suivre les ressources
                        pedagogiques — accessible aux eleves, enseignants et a l'administration.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <x-button variant="primary" size="lg" href="{{ route('login') }}" class="!bg-white !text-bns-primary hover:!bg-teal-50">
                            <x-icon name="arrow-right" class="h-4 w-4" /> Se connecter
                        </x-button>
                        <x-button variant="ghost" size="lg" href="{{ route('register') }}" class="!text-white ring-1 ring-white/30 hover:!bg-white/10">
                            Creer un compte eleve
                        </x-button>
                    </div>
                    <p class="mt-6 text-xs text-teal-200">
                        Reserve aux membres de l'etablissement. Les comptes sont crees ou valides par l'administration.
                    </p>
                </div>

                <div class="bns-fade-in-up relative hidden lg:block" style="animation-delay:.1s">
                    <div class="relative mx-auto h-80 w-full max-w-sm">
                        <div class="absolute inset-x-6 bottom-0 top-10 rotate-3 rounded-2xl bg-white/10 ring-1 ring-white/15"></div>
                        <div class="absolute inset-x-3 bottom-0 top-5 -rotate-2 rounded-2xl bg-white/15 ring-1 ring-white/20"></div>
                        <div class="absolute inset-0 flex flex-col justify-between rounded-2xl bg-white p-6 shadow-2xl">
                            <div class="flex items-center justify-between">
                                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-600/10 text-bns-primary">
                                    <x-icon name="book-open" class="h-5 w-5" />
                                </span>
                                <x-badge color="success">Publie</x-badge>
                            </div>
                            <div>
                                <p class="font-heading text-lg font-semibold text-bns-foreground">Mathematiques — 4e annee</p>
                                <p class="mt-1 text-sm text-bns-muted-foreground">Manuel officiel, chapitres 1 a 12</p>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-bns-muted-foreground">
                                <span class="flex items-center gap-1"><x-icon name="users" class="h-3.5 w-3.5" /> 214 eleves</span>
                                <span class="flex items-center gap-1"><x-icon name="chart-bar" class="h-3.5 w-3.5" /> 1 240 lectures</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="fonctionnalites" class="mx-auto max-w-6xl px-4 py-20">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="font-heading text-3xl font-semibold text-bns-foreground">Concu pour la vie scolaire</h2>
                <p class="mt-3 text-bns-muted-foreground">Une seule plateforme pour publier, decouvrir et suivre les ressources pedagogiques de l'etablissement.</p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @php
                $fonctionnalites = [
                    ['icon' => 'book-open', 'titre' => 'Catalogue numerique', 'texte' => 'Manuels organises par niveau et par matiere, mis a jour directement par les enseignants.'],
                    ['icon' => 'device', 'titre' => 'Lecture sur tout appareil', 'texte' => 'Consultez vos manuels PDF et EPUB depuis un ordinateur, une tablette ou un smartphone.'],
                    ['icon' => 'bolt', 'titre' => 'Reprise instantanee', 'texte' => 'Retrouvez votre progression de lecture exactement la ou vous vous etiez arrete.'],
                    ['icon' => 'shield-check', 'titre' => 'Acces securise', 'texte' => 'Chaque compte est protege et reserve aux membres verifies de l\'etablissement.'],
                    ['icon' => 'chart-bar', 'titre' => 'Suivi pedagogique', 'texte' => 'Enseignants et administration suivent l\'usage des ressources en temps reel.'],
                    ['icon' => 'users', 'titre' => 'Pense pour tous les niveaux', 'texte' => 'Une experience adaptee aux eleves, aux enseignants et a l\'administration.'],
                ];
                @endphp

                @foreach ($fonctionnalites as $f)
                    <div class="rounded-xl border border-bns-border bg-bns-card p-6 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-teal-600/10 text-bns-primary">
                            <x-icon :name="$f['icon']" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-4 font-heading text-base font-semibold text-bns-foreground">{{ $f['titre'] }}</h3>
                        <p class="mt-2 text-sm text-bns-muted-foreground">{{ $f['texte'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="comment-ca-marche" class="border-y border-bns-border bg-bns-muted/40">
            <div class="mx-auto max-w-6xl px-4 py-20">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="font-heading text-3xl font-semibold text-bns-foreground">Comment ca marche</h2>
                    <p class="mt-3 text-bns-muted-foreground">Trois etapes suffisent pour acceder a vos ressources pedagogiques.</p>
                </div>

                <div class="mt-12 grid gap-8 sm:grid-cols-3">
                    @php
                    $etapes = [
                        ['icon' => 'lock', 'titre' => 'Connectez-vous', 'texte' => 'Utilisez l\'identifiant fourni par votre etablissement pour acceder a votre espace.'],
                        ['icon' => 'search', 'titre' => 'Parcourez le catalogue', 'texte' => 'Filtrez par matiere ou par niveau pour retrouver le manuel dont vous avez besoin.'],
                        ['icon' => 'book-open', 'titre' => 'Lisez en ligne', 'texte' => 'Ouvrez le lecteur integre et reprenez votre lecture a tout moment, sur tout appareil.'],
                    ];
                    @endphp
                    @foreach ($etapes as $i => $e)
                        <div class="relative text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-bns-primary text-white shadow-sm">
                                <x-icon :name="$e['icon']" class="h-6 w-6" />
                            </div>
                            <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-bns-primary">Etape {{ $i + 1 }}</p>
                            <h3 class="mt-1 font-heading text-base font-semibold text-bns-foreground">{{ $e['titre'] }}</h3>
                            <p class="mx-auto mt-2 max-w-xs text-sm text-bns-muted-foreground">{{ $e['texte'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-20">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-800 via-teal-700 to-teal-900 px-8 py-14 text-center shadow-lg">
                <div class="bns-blob bns-blob-1" aria-hidden="true"></div>
                <div class="relative z-10">
                    <h2 class="font-heading text-2xl font-semibold text-white sm:text-3xl">Pret a acceder a votre bibliotheque ?</h2>
                    <p class="mx-auto mt-3 max-w-lg text-teal-100">Connectez-vous avec votre identifiant d'etablissement ou creez un compte eleve en quelques instants.</p>
                    <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
                        <x-button variant="primary" size="lg" href="{{ route('login') }}" class="!bg-white !text-bns-primary hover:!bg-teal-50">
                            Se connecter
                        </x-button>
                        <x-button variant="ghost" size="lg" href="{{ route('register') }}" class="!text-white ring-1 ring-white/30 hover:!bg-white/10">
                            Creer un compte eleve
                        </x-button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-bns-border">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-8 text-sm text-bns-muted-foreground sm:flex-row">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-md bg-bns-primary font-heading text-[10px] font-bold text-white">BNS</span>
                <span>Bibliotheque Numerique Scolaire</span>
            </div>
            <p>© {{ date('Y') }} — Usage interne a l'etablissement.</p>
        </div>
    </footer>
</body>
</html>
