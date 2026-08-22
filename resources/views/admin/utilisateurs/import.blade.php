@extends('layouts.adminlte')

@section('page-title', 'Importer des eleves par CSV')
@section('page-description', 'Creation en masse de comptes eleves depuis un fichier CSV.')

@section('page-actions')
    <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-circle-right fa-flip-horizontal"></i> Retour aux utilisateurs
    </a>
@endsection

@section('adminlte-contenu')
<div class="card bns-reveal" style="max-width:48rem;">
    <div class="card-body">
        <div class="alert alert-info d-flex align-items-start">
            <i class="fas fa-download mt-1 mr-2"></i>
            <div>
                Le fichier CSV doit contenir une ligne d'en-tete avec les colonnes :
                <code>nom,prenom,identifiant,niveau,classe</code>.
                Le mot de passe initial est genere aleatoirement (reinitialisable ensuite depuis la liste des utilisateurs).
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.utilisateurs.importer') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="csv">Fichier CSV</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input @error('csv') is-invalid @enderror" id="csv" name="csv" accept=".csv,text/csv" required>
                    <label class="custom-file-label" for="csv">Choisir un fichier...</label>
                    @error('csv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-download"></i> Importer
            </button>
        </form>
    </div>
</div>
@endsection
