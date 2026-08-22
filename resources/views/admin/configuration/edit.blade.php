@extends('layouts.adminlte')

@section('page-title', 'Configuration systeme')
@section('page-description', "Parametres globaux de l'etablissement et de la plateforme.")

@section('adminlte-contenu')
<div class="card bns-reveal" style="max-width:48rem;">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.configuration.update') }}">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Etablissement</h3>
                    <p class="text-muted small mb-0">Informations affichees sur les ecrans publics et de connexion.</p>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="etablissement_nom">Nom de l'etablissement</label>
                        <input type="text" id="etablissement_nom" name="etablissement_nom" required value="{{ old('etablissement_nom', $parametres['etablissement_nom']) }}" class="form-control @error('etablissement_nom') is-invalid @enderror">
                        @error('etablissement_nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-0">
                        <label for="etablissement_logo">URL du logo (optionnel)</label>
                        <input type="text" id="etablissement_logo" name="etablissement_logo" value="{{ old('etablissement_logo', $parametres['etablissement_logo']) }}" class="form-control @error('etablissement_logo') is-invalid @enderror">
                        @error('etablissement_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fichiers et securite</h3>
                    <p class="text-muted small mb-0">Contraintes appliquees aux imports et aux mots de passe.</p>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-sm-6">
                            <label for="taille_max_fichier_mo">Taille max des fichiers (Mo)</label>
                            <input type="number" id="taille_max_fichier_mo" name="taille_max_fichier_mo" required value="{{ old('taille_max_fichier_mo', $parametres['taille_max_fichier_mo']) }}" class="form-control @error('taille_max_fichier_mo') is-invalid @enderror">
                            @error('taille_max_fichier_mo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="politique_mdp_longueur_min">Longueur min. du mot de passe</label>
                            <input type="number" id="politique_mdp_longueur_min" name="politique_mdp_longueur_min" required value="{{ old('politique_mdp_longueur_min', $parametres['politique_mdp_longueur_min']) }}" class="form-control @error('politique_mdp_longueur_min') is-invalid @enderror">
                            @error('politique_mdp_longueur_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="formats_autorises">Formats autorises (separes par virgule)</label>
                        <input type="text" id="formats_autorises" name="formats_autorises" required value="{{ old('formats_autorises', $parametres['formats_autorises']) }}" class="form-control @error('formats_autorises') is-invalid @enderror">
                        @error('formats_autorises')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-0">
                        <label for="duree_conservation_consultations_jours">Duree de conservation des consultations (jours)</label>
                        <input type="number" id="duree_conservation_consultations_jours" name="duree_conservation_consultations_jours" required value="{{ old('duree_conservation_consultations_jours', $parametres['duree_conservation_consultations_jours']) }}" class="form-control @error('duree_conservation_consultations_jours') is-invalid @enderror">
                        @error('duree_conservation_consultations_jours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inscriptions</h3>
                </div>
                <div class="card-body">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="validation_auto" name="validation_auto" value="1"
                            @checked(old('validation_auto', $parametres['validation_auto'] === 'true'))>
                        <label class="custom-control-label" for="validation_auto">Valider automatiquement les nouvelles inscriptions eleves (sinon validation manuelle par un administrateur)</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
