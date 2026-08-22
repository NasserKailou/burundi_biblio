@php
    $niveauxSelectionnes = old('niveaux', isset($manuel) ? $manuel->niveaux->pluck('id')->all() : []);
@endphp

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informations generales</h3>
            <p class="text-muted small mb-0">Le titre et la matiere permettent aux eleves de retrouver le manuel dans le catalogue.</p>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="titre">Titre</label>
                    <input type="text" id="titre" name="titre" required value="{{ old('titre', $manuel->titre ?? '') }}" class="form-control @error('titre') is-invalid @enderror">
                    @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group col-sm-6">
                    <label for="matiere_id">Matiere</label>
                    <select id="matiere_id" name="matiere_id" required class="form-control @error('matiere_id') is-invalid @enderror">
                        <option value="">-- Choisir --</option>
                        @foreach ($matieres as $matiere)
                            <option value="{{ $matiere->id }}" @selected(old('matiere_id', $manuel->matiere_id ?? null) == $matiere->id)>{{ $matiere->libelle }}</option>
                        @endforeach
                    </select>
                    @error('matiere_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label for="uploader_id">Proprietaire (enseignant)</label>
                <select id="uploader_id" name="uploader_id" required class="form-control @error('uploader_id') is-invalid @enderror">
                    <option value="">-- Choisir --</option>
                    @foreach ($enseignants as $enseignant)
                        <option value="{{ $enseignant->id }}" @selected(old('uploader_id', $manuel->uploader_id ?? null) == $enseignant->id)>{{ $enseignant->nomComplet() }}</option>
                    @endforeach
                </select>
                @error('uploader_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $manuel->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="auteur">Auteur (optionnel)</label>
                    <input type="text" id="auteur" name="auteur" value="{{ old('auteur', $manuel->auteur ?? '') }}" class="form-control @error('auteur') is-invalid @enderror">
                    @error('auteur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group col-sm-6">
                    <label for="annee">Annee (optionnelle)</label>
                    <input type="number" id="annee" name="annee" value="{{ old('annee', $manuel->annee ?? '') }}" class="form-control @error('annee') is-invalid @enderror">
                    @error('annee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Diffusion</h3>
            <p class="text-muted small mb-0">Choisissez qui peut voir ce manuel dans son espace eleve.</p>
        </div>
        <div class="card-body">
            <fieldset class="form-group">
                <legend class="col-form-label">Niveaux cibles</legend>
                <div>
                    @foreach ($niveaux as $niveau)
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="niveau_cible_{{ $niveau->id }}" name="niveaux[]" value="{{ $niveau->id }}"
                                @checked(in_array($niveau->id, $niveauxSelectionnes))>
                            <label class="custom-control-label" for="niveau_cible_{{ $niveau->id }}">{{ $niveau->libelle }}</label>
                        </div>
                    @endforeach
                </div>
            </fieldset>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="est_commun" name="est_commun" value="1"
                        @checked(old('est_commun', $manuel->est_commun ?? false))>
                    <label class="custom-control-label" for="est_commun">Ressource commune (visible par tous les niveaux)</label>
                </div>
            </div>

            <fieldset class="form-group mb-0">
                <legend class="col-form-label">Statut</legend>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="statut_brouillon" class="custom-control-input" name="statut" value="brouillon"
                        @checked(old('statut', $manuel->statut ?? 'brouillon') === 'brouillon')>
                    <label class="custom-control-label" for="statut_brouillon">Brouillon</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="statut_publie" class="custom-control-input" name="statut" value="publie"
                        @checked(old('statut', $manuel->statut ?? '') === 'publie')>
                    <label class="custom-control-label" for="statut_publie">Publie</label>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Fichiers</h3>
            <p class="text-muted small mb-0">Formats acceptes : PDF ou EPUB pour le contenu, image pour la couverture.</p>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="fichier">Fichier (PDF/EPUB) @if(!isset($manuel))<span class="text-danger">*</span>@endif</label>
                    <div class="custom-file">
                        <input type="file" id="fichier" name="fichier" accept=".pdf,.epub" class="custom-file-input @error('fichier') is-invalid @enderror">
                        <label class="custom-file-label" for="fichier">Choisir un fichier...</label>
                        @error('fichier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @if (isset($manuel))<small class="form-text text-muted">Laisser vide pour conserver le fichier actuel.</small>@endif
                </div>
                <div class="form-group col-sm-6">
                    <label for="couverture">Couverture @if(!isset($manuel))<span class="text-danger">*</span>@endif</label>
                    <div class="custom-file">
                        <input type="file" id="couverture" name="couverture" accept="image/*" class="custom-file-input @error('couverture') is-invalid @enderror">
                        <label class="custom-file-label" for="couverture">Choisir un fichier...</label>
                        @error('couverture')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @if (isset($manuel))<small class="form-text text-muted">Laisser vide pour conserver la couverture actuelle.</small>@endif
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-end mt-3">
        <a href="{{ route('admin.manuels.index') }}" class="btn btn-link text-muted mr-2">Annuler</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-check-circle"></i>
            {{ isset($manuel) ? 'Enregistrer les modifications' : 'Ajouter le manuel' }}
        </button>
    </div>
</div>
