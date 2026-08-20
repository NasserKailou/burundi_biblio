@extends('layouts.app')

@section('titre', 'Statistiques')

@section('contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Statistiques de mon niveau</h1>

<div class="mb-8 grid grid-cols-2 gap-6 sm:grid-cols-4">
    <x-stat-card label="Manuels" :value="$overview['nb_manuels']" />
    <x-stat-card label="Consultations" :value="$overview['nb_consultations']" />
    <x-stat-card label="Heures de lecture" :value="$overview['duree_totale_heures']" />
    <x-stat-card label="Eleves actifs" :value="$overview['nb_eleves_actifs']" />
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

<div class="grid gap-6 lg:grid-cols-2">
    <x-card>
        <h2 class="mb-3 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Manuels les plus consultes (mon niveau)</h2>
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
</div>
@endsection

@push('scripts')
    @vite(['resources/js/stats.js'])
@endpush
