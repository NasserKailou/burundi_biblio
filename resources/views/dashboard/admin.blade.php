@extends('layouts.adminlte')

@section('page-title', 'Tableau de bord administrateur')
@section('page-description', "Vue d'ensemble de l'activite de la bibliotheque numerique.")

@section('adminlte-contenu')
<div class="row bns-reveal-list">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $nbUtilisateurs }}</h3>
                <p>Utilisateurs</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ route('admin.utilisateurs.index') }}" class="small-box-footer">
                Voir les comptes <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box" style="background-color:#e08e00;color:#fff;">
            <div class="inner">
                <h3>{{ $nbUtilisateursEnAttente }}</h3>
                <p>En attente de validation</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
            <a href="{{ route('admin.utilisateurs.index') }}" class="small-box-footer">
                Traiter <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $nbManuels }}</h3>
                <p>Manuels</p>
            </div>
            <div class="icon"><i class="fas fa-book-open"></i></div>
            <a href="{{ route('admin.manuels.index') }}" class="small-box-footer">
                Voir le catalogue <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row bns-reveal-list">
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('admin.utilisateurs.index') }}" class="card card-hover text-decoration-none">
            <div class="card-body d-flex align-items-center">
                <span class="icon-circle bg-primary text-white mr-3"><i class="fas fa-users"></i></span>
                <div>
                    <p class="mb-0 font-weight-bold text-dark">Utilisateurs</p>
                    <p class="mb-0 text-muted small">Gerer les comptes</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('admin.manuels.create') }}" class="card card-hover text-decoration-none">
            <div class="card-body d-flex align-items-center">
                <span class="icon-circle bg-primary text-white mr-3"><i class="fas fa-plus"></i></span>
                <div>
                    <p class="mb-0 font-weight-bold text-dark">Ajouter un manuel</p>
                    <p class="mb-0 text-muted small">Publier une ressource</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('admin.statistiques.index') }}" class="card card-hover text-decoration-none">
            <div class="card-body d-flex align-items-center">
                <span class="icon-circle bg-primary text-white mr-3"><i class="fas fa-chart-column"></i></span>
                <div>
                    <p class="mb-0 font-weight-bold text-dark">Statistiques</p>
                    <p class="mb-0 text-muted small">Suivre l'usage</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('admin.audit.index') }}" class="card card-hover text-decoration-none">
            <div class="card-body d-flex align-items-center">
                <span class="icon-circle bg-primary text-white mr-3"><i class="fas fa-shield-halved"></i></span>
                <div>
                    <p class="mb-0 font-weight-bold text-dark">Journaux d'audit</p>
                    <p class="mb-0 text-muted small">Controler la securite</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
