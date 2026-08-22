@extends('layouts.adminlte')

@section('page-title', $manuel->titre)

@section('adminlte-contenu')
<a href="{{ route('catalogue.index') }}" class="mb-3 d-inline-block text-muted small">
    <i class="fas fa-arrow-left"></i> Retour au catalogue
</a>

<div class="row bns-reveal">
    <div class="col-md-3 mb-4">
        <div class="card">
            <img src="{{ route('catalogue.couverture', $manuel) }}" class="card-img-top" alt="Couverture du manuel {{ $manuel->titre }}" loading="lazy">
        </div>
    </div>

    <div class="col-md-9">
        <h1>{{ $manuel->titre }}</h1>

        <div class="mt-2 mb-3">
            <span class="badge badge-primary mr-1">{{ $manuel->matiere->libelle }}</span>
            <span class="badge badge-secondary mr-1">{{ strtoupper($manuel->type) }}</span>
            @foreach ($manuel->niveaux as $niveau)
                <span class="badge badge-secondary mr-1">{{ $niveau->libelle }}</span>
            @endforeach
            @if ($manuel->est_commun)
                <span class="badge badge-success mr-1">Commun a tous les niveaux</span>
            @endif
        </div>

        <dl class="text-muted small mb-0">
            @if ($manuel->auteur)
                <div><dt class="d-inline font-weight-bold text-dark">Auteur : </dt><dd class="d-inline">{{ $manuel->auteur }}</dd></div>
            @endif
            @if ($manuel->annee)
                <div><dt class="d-inline font-weight-bold text-dark">Annee : </dt><dd class="d-inline">{{ $manuel->annee }}</dd></div>
            @endif
        </dl>

        @if ($manuel->description)
            <p class="mt-3">{{ $manuel->description }}</p>
        @endif

        <div class="mt-4" id="actions-manuel" data-manuel-id="{{ $manuel->id }}" data-favori="{{ $estFavori ? '1' : '0' }}" data-favori-url="{{ route('api.favoris.store') }}" data-favori-destroy-url="{{ route('api.favoris.destroy', $manuel) }}">
            <a href="{{ route('reader.show', $manuel) }}" class="btn btn-primary">
                <i class="fas fa-book-open"></i> Lire
            </a>
            <button id="btn-favori-fiche" type="button" class="btn btn-outline-secondary ml-2" aria-pressed="{{ $estFavori ? 'true' : 'false' }}">
                {{ $estFavori ? 'Retirer des favoris' : 'Ajouter aux favoris' }}
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/fiche-manuel.js'])
@endpush
