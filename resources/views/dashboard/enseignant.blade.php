@extends('layouts.adminlte')

@section('page-title', "Bonjour {$user->prenom}")
@section('page-description', "Vue d'ensemble de vos publications sur la plateforme.")

@section('adminlte-contenu')
<div class="row bns-reveal-list">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $nbManuels }}</h3>
                <p>Manuels</p>
            </div>
            <div class="icon"><i class="fas fa-book-open"></i></div>
            <a href="{{ route('teacher.manuels.index') }}" class="small-box-footer">
                Voir mes manuels <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $nbPublies }}</h3>
                <p>Publies</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="{{ route('teacher.manuels.index') }}" class="small-box-footer">
                Voir mes manuels <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-layer-group mr-1"></i> Niveaux geres</h3>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    {{ $user->niveau?->libelle }}@if($user->niveauxEnseignes->isNotEmpty()), {{ $user->niveauxEnseignes->pluck('libelle')->join(', ') }}@endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
