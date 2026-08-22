{{-- Lecteur immersif plein ecran (fixed inset-0 z-40) : ne s'etend d'aucun layout
     partage. Volontairement independant d'AdminLTE (le back-office recolore en
     Bootstrap ne charge pas les classes utilitaires Tailwind qu'utilise ce lecteur) et
     d'un chrome de navigation qu'il masquerait de toute facon a l'ecran. Contenu
     interne (div#lecteur et tout ce qu'il contient) volontairement non modifie -
     integration PDF.js/EPUB.js fragile, voir context.md etape 6. --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $manuel->titre }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bns-reader font-sans text-bns-foreground antialiased">
<div
    id="lecteur"
    class="fixed inset-0 z-40 flex flex-col bg-bns-reader"
    data-manuel-id="{{ $manuel->id }}"
    data-type="{{ $manuel->type }}"
    data-fichier-url="{{ route('reader.fichier', ['manuel' => $manuel, 'nom' => 'manuel.'.$manuel->type]) }}"
    data-derniere-page="{{ $dernierePage ?? '' }}"
    data-favori="{{ $estFavori ? '1' : '0' }}"
    data-favori-url="{{ route('api.favoris.store') }}"
    data-favori-destroy-url="{{ route('api.favoris.destroy', $manuel) }}"
>
    <div class="flex items-center justify-between gap-4 bg-bns-reader-toolbar px-4 py-2 text-white shadow-md">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('catalogue.show', $manuel) }}" class="shrink-0 rounded p-2 hover:bg-white/10" aria-label="Retour a la fiche du manuel">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <span class="truncate text-sm font-medium">{{ $manuel->titre }}</span>
        </div>

        <div class="flex items-center gap-1">
            <button type="button" id="btn-precedent" class="rounded p-2 hover:bg-white/10" aria-label="Page precedente">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <span class="min-w-[5rem] text-center text-sm tabular-nums" id="indicateur-position" aria-live="polite">- / -</span>
            <button type="button" id="btn-suivant" class="rounded p-2 hover:bg-white/10" aria-label="Page suivante">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </button>

            <span class="mx-2 h-5 w-px bg-white/20"></span>

            <button type="button" id="btn-zoom-arriere" class="rounded p-2 hover:bg-white/10" aria-label="Zoom arriere">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" /></svg>
            </button>
            <button type="button" id="btn-zoom-avant" class="rounded p-2 hover:bg-white/10" aria-label="Zoom avant">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            </button>

            <span class="mx-2 h-5 w-px bg-white/20"></span>

            <div class="relative">
                <button type="button" id="btn-signets" class="rounded p-2 hover:bg-white/10" aria-label="Signets" aria-expanded="false" aria-controls="panneau-signets">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-4-7 4V5z" /></svg>
                </button>
                <div id="panneau-signets" class="absolute right-0 top-full mt-2 hidden w-64 rounded-md border border-bns-border bg-white p-3 text-bns-foreground shadow-lg">
                    <button type="button" id="btn-ajouter-signet" class="mb-2 w-full rounded-md bg-bns-primary px-3 py-1.5 text-sm font-medium text-bns-on-primary hover:bg-teal-800">
                        Ajouter un signet ici
                    </button>
                    <ul id="liste-signets" class="max-h-48 space-y-1 overflow-y-auto text-sm"></ul>
                </div>
            </div>

            <button type="button" id="btn-favori" class="rounded p-2 hover:bg-white/10" aria-label="Ajouter aux favoris" aria-pressed="false">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="icone-favori" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
            </button>

            <button type="button" id="btn-plein-ecran" class="rounded p-2 hover:bg-white/10" aria-label="Plein ecran">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
            </button>
        </div>
    </div>

    <div id="zone-lecture" class="flex-1 overflow-auto">
        <div id="lecteur-pdf-conteneur" class="hidden h-full items-center justify-center overflow-auto p-4">
            <canvas id="canvas-pdf" class="mx-auto shadow-lg"></canvas>
        </div>
        <div id="lecteur-epub-conteneur" class="hidden h-full"></div>
    </div>

    <p id="lecteur-statut" class="sr-only" role="status" aria-live="polite"></p>
</div>

@vite(['resources/js/reader.js'])
</body>
</html>
