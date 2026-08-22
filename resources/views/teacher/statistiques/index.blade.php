@extends('layouts.adminlte')

@section('page-title', 'Statistiques de mon niveau')
@section('page-description', 'Usage des manuels que vous avez publies.')

@section('adminlte-contenu')
<div class="row bns-reveal-list">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $overview['nb_manuels'] }}</h3>
                <p>Manuels</p>
            </div>
            <div class="icon"><i class="fas fa-book-open"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background-color:#e08e00;color:#fff;">
            <div class="inner">
                <h3>{{ $overview['nb_consultations'] }}</h3>
                <p>Consultations</p>
            </div>
            <div class="icon"><i class="fas fa-chart-column"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $overview['duree_totale_heures'] }}</h3>
                <p>Heures de lecture</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $overview['nb_eleves_actifs'] }}</h3>
                <p>Eleves actifs</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
</div>

<div class="row bns-reveal-list">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Consultations dans le temps</h3>
            </div>
            <div class="card-body">
                <x-chart type="line" :labels="$consultationsParPeriode->pluck('periode')" :values="$consultationsParPeriode->pluck('total')" label="Consultations" />
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Repartition par matiere</h3>
            </div>
            <div class="card-body">
                <x-chart type="doughnut" :labels="$repartitionMatiere->pluck('libelle')" :values="$repartitionMatiere->pluck('total')" label="Manuels par matiere" />
            </div>
        </div>
    </div>
</div>

<div class="row bns-reveal-list">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manuels les plus consultes (mon niveau)</h3>
            </div>
            <div class="card-body">
                <ol class="list-unstyled mb-0">
                    @forelse ($manuelsPlusConsultes as $ligne)
                        <li class="d-flex justify-content-between py-1">
                            <span class="text-truncate">{{ $ligne['titre'] }}</span>
                            <span class="text-muted ml-2">{{ $ligne['nb_consultations'] }}</span>
                        </li>
                    @empty
                        <li class="text-muted">Aucune donnee.</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Eleves les plus actifs</h3>
            </div>
            <div class="card-body">
                <ol class="list-unstyled mb-0">
                    @forelse ($elevesPlusActifs as $ligne)
                        <li class="d-flex justify-content-between py-1">
                            <span class="text-truncate">{{ $ligne['nom'] }} <span class="text-muted">({{ $ligne['niveau'] }})</span></span>
                            <span class="text-muted ml-2">{{ $ligne['nb_consultations'] }}</span>
                        </li>
                    @empty
                        <li class="text-muted">Aucune donnee.</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/stats.js'])
@endpush
