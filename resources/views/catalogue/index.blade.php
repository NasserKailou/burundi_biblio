@extends('layouts.adminlte')

@section('page-title', 'Catalogue')
@section('page-description', 'Parcourez les manuels disponibles pour votre niveau.')

@section('adminlte-contenu')
<div class="row">
    <aside class="col-md-3 mb-4">
        <div class="card bns-reveal">
            <div class="card-body">
                <div class="form-group">
                    <label for="recherche">Rechercher</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-magnifying-glass"></i></span>
                        </div>
                        <input id="recherche" type="search" class="form-control" placeholder="Titre, auteur, mot-cle...">
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label for="filtre-matiere">Matiere</label>
                    <select id="filtre-matiere" class="form-control">
                        <option value="">Toutes les matieres</option>
                        @foreach ($matieres as $matiere)
                            <option value="{{ $matiere->id }}">{{ $matiere->libelle }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </aside>

    <div class="col-md-9">
        <p id="catalogue-statut" class="text-muted mb-3" role="status" aria-live="polite"></p>

        <div id="catalogue-grille" class="row bns-reveal-list"></div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/catalogue.js'])
@endpush
