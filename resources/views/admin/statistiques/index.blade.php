@extends('layouts.admin')

@section('titre', 'Statistiques')

@section('admin-contenu')
<x-page-header title="Statistiques" description="Usage du catalogue et de la plateforme." icon="chart-bar">
    <x-slot:actions>
        <x-button variant="secondary" href="{{ route('admin.statistiques.export', ['format' => 'csv', 'niveau' => $niveauSelectionne]) }}">
            <x-icon name="download" class="h-4 w-4" /> CSV
        </x-button>
        <x-button variant="secondary" href="{{ route('admin.statistiques.export', ['format' => 'pdf', 'niveau' => $niveauSelectionne]) }}">
            <x-icon name="download" class="h-4 w-4" /> PDF
        </x-button>
    </x-slot:actions>
</x-page-header>

<form method="GET" class="mb-6 flex flex-wrap items-center gap-3 rounded-xl border border-bns-border bg-bns-card p-3 shadow-sm">
    <select name="niveau" class="rounded-md border border-bns-border bg-white px-3 py-2 text-sm shadow-sm">
        <option value="">Tous les niveaux</option>
        @foreach ($niveaux as $niveau)
            <option value="{{ $niveau->id }}" @selected($niveauSelectionne == $niveau->id)>{{ $niveau->libelle }}</option>
        @endforeach
    </select>
    <select name="granularite" class="rounded-md border border-bns-border bg-white px-3 py-2 text-sm shadow-sm">
        <option value="jour" @selected($granularite === 'jour')>Par jour</option>
        <option value="semaine" @selected($granularite === 'semaine')>Par semaine</option>
        <option value="mois" @selected($granularite === 'mois')>Par mois</option>
    </select>
    <button type="submit" class="inline-flex items-center gap-2 rounded-md border border-bns-border px-4 py-2 text-sm font-medium text-bns-foreground transition-colors hover:bg-bns-muted">
        <x-icon name="filter" class="h-4 w-4" /> Filtrer
    </button>
</form>

<div class="mb-8 grid grid-cols-2 gap-6 sm:grid-cols-4">
    <x-stat-card label="Manuels" :value="$overview['nb_manuels']" icon="book-open" accent="primary" />
    <x-stat-card label="Consultations" :value="$overview['nb_consultations']" icon="chart-bar" accent="accent" />
    <x-stat-card label="Heures de lecture" :value="$overview['duree_totale_heures']" icon="clock" accent="success" />
    <x-stat-card label="Eleves actifs" :value="$overview['nb_eleves_actifs']" icon="users" accent="primary" />
</div>

<div class="mb-8 grid gap-6 lg:grid-cols-2">
    <x-card>
        <h2 class="mb-4 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Consultations dans le temps</h2>
        <x-chart type="line" :labels="$consultationsParPeriode->pluck('periode')" :values="$consultationsParPeriode->pluck('total')" label="Consultations" />
    </x-card>
    <x-card>
        <h2 class="mb-4 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Repartition par matiere</h2>
        <x-chart type="doughnut" :labels="$repartitionMatiere->pluck('libelle')" :values="$repartitionMatiere->pluck('total')" label="Manuels par matiere" />
    </x-card>
</div>

<div class="mb-8">
    <x-card>
        <h2 class="mb-4 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Repartition par niveau</h2>
        <x-chart type="bar" :labels="$repartitionNiveau->pluck('libelle')" :values="$repartitionNiveau->pluck('total')" label="Manuels par niveau" />
    </x-card>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <x-card>
        <h2 class="mb-3 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Manuels les plus consultes</h2>
        <ol class="space-y-2 text-sm">
            @forelse ($manuelsPlusConsultes as $ligne)
                <li class="flex justify-between gap-2">
                    <span class="truncate text-bns-foreground">{{ $ligne['titre'] }}</span>
                    <span class="shrink-0 text-bns-muted-foreground">{{ $ligne['nb_consultations'] }}</span>
                </li>
            @empty
                <li class="text-bns-muted-foreground">Aucune donnee.</li>
            @endforelse
        </ol>
    </x-card>
    <x-card>
        <h2 class="mb-3 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Eleves les plus actifs</h2>
        <ol class="space-y-2 text-sm">
            @forelse ($elevesPlusActifs as $ligne)
                <li class="flex justify-between gap-2">
                    <span class="truncate text-bns-foreground">{{ $ligne['nom'] }} <span class="text-bns-muted-foreground">({{ $ligne['niveau'] }})</span></span>
                    <span class="shrink-0 text-bns-muted-foreground">{{ $ligne['nb_consultations'] }}</span>
                </li>
            @empty
                <li class="text-bns-muted-foreground">Aucune donnee.</li>
            @endforelse
        </ol>
    </x-card>
    <x-card>
        <h2 class="mb-3 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Enseignants les plus actifs</h2>
        <ol class="space-y-2 text-sm">
            @forelse ($enseignantsPlusActifs as $ligne)
                <li class="flex justify-between gap-2">
                    <span class="truncate text-bns-foreground">{{ $ligne['nom'] }}</span>
                    <span class="shrink-0 text-bns-muted-foreground">{{ $ligne['nb_consultations_recues'] }}</span>
                </li>
            @empty
                <li class="text-bns-muted-foreground">Aucune donnee.</li>
            @endforelse
        </ol>
    </x-card>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/stats.js'])
@endpush
