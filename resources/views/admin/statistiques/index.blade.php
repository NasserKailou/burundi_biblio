@extends('layouts.adminlte')

@section('page-title', 'Statistiques')
@section('page-description', "Usage du catalogue et de la plateforme.")

@section('page-actions')
    <a href="{{ route('admin.statistiques.export', ['format' => 'csv', 'niveau' => $niveauSelectionne]) }}" class="btn btn-outline-secondary">
        <i class="fas fa-download"></i> CSV
    </a>
    <a href="{{ route('admin.statistiques.export', ['format' => 'pdf', 'niveau' => $niveauSelectionne]) }}" class="btn btn-outline-secondary">
        <i class="fas fa-download"></i> PDF
    </a>
@endsection

@section('adminlte-contenu')

<form method="GET" class="form-inline card card-body bns-reveal mb-4">
    <select name="niveau" class="form-control mr-2 mb-2">
        <option value="">Tous les niveaux</option>
        @foreach ($niveaux as $niveau)
            <option value="{{ $niveau->id }}" @selected($niveauSelectionne == $niveau->id)>{{ $niveau->libelle }}</option>
        @endforeach
    </select>
    <select name="granularite" class="form-control mr-2 mb-2">
        <option value="jour" @selected($granularite === 'jour')>Par jour</option>
        <option value="semaine" @selected($granularite === 'semaine')>Par semaine</option>
        <option value="mois" @selected($granularite === 'mois')>Par mois</option>
    </select>
    <button type="submit" class="btn btn-outline-secondary mb-2">
        <i class="fas fa-filter"></i> Filtrer
    </button>
</form>

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
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Repartition par niveau</h3>
            </div>
            <div class="card-body">
                <x-chart type="bar" :labels="$repartitionNiveau->pluck('libelle')" :values="$repartitionNiveau->pluck('total')" label="Manuels par niveau" />
            </div>
        </div>
    </div>
</div>

<div class="row bns-reveal-list">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manuels les plus consultes</h3>
            </div>
            <div class="card-body">
                <ol class="list-unstyled mb-0">
                    @forelse ($manuelsPlusConsultes as $ligne)
                        <li class="d-flex justify-content-between mb-2">
                            <span class="text-truncate mr-2">{{ $ligne['titre'] }}</span>
                            <span class="text-muted text-nowrap">{{ $ligne['nb_consultations'] }}</span>
                        </li>
                    @empty
                        <li class="text-muted">Aucune donnee.</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Eleves les plus actifs</h3>
            </div>
            <div class="card-body">
                <ol class="list-unstyled mb-0">
                    @forelse ($elevesPlusActifs as $ligne)
                        <li class="d-flex justify-content-between mb-2">
                            <span class="text-truncate mr-2">{{ $ligne['nom'] }} <span class="text-muted">({{ $ligne['niveau'] }})</span></span>
                            <span class="text-muted text-nowrap">{{ $ligne['nb_consultations'] }}</span>
                        </li>
                    @empty
                        <li class="text-muted">Aucune donnee.</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Enseignants les plus actifs</h3>
            </div>
            <div class="card-body">
                <ol class="list-unstyled mb-0">
                    @forelse ($enseignantsPlusActifs as $ligne)
                        <li class="d-flex justify-content-between mb-2">
                            <span class="text-truncate mr-2">{{ $ligne['nom'] }}</span>
                            <span class="text-muted text-nowrap">{{ $ligne['nb_consultations_recues'] }}</span>
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
