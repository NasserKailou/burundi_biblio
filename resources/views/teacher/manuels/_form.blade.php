@php
    $niveauxSelectionnes = old('niveaux', isset($manuel) ? $manuel->niveaux->pluck('id')->all() : []);
@endphp

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informations generales</h3>
        </div>
        <div class="card-body">
            <p class="text-muted small">Le titre et la matiere permettent aux eleves de retrouver le manuel dans le catalogue.</p>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="titre">Titre <span class="text-danger">*</span></label>
                    <input type="text" id="titre" name="titre" required
                        class="form-control @error('titre') is-invalid @enderror"
                        value="{{ old('titre', $manuel->titre ?? '') }}">
                    @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="matiere_id">Matiere <span class="text-danger">*</span></label>
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
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $manuel->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="auteur">Auteur (optionnel)</label>
                    <input type="text" id="auteur" name="auteur" class="form-control @error('auteur') is-invalid @enderror"
                        value="{{ old('auteur', $manuel->auteur ?? '') }}">
                    @error('auteur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="annee">Annee (optionnelle)</label>
                    <input type="number" id="annee" name="annee" class="form-control @error('annee') is-invalid @enderror"
                        value="{{ old('annee', $manuel->annee ?? '') }}">
                    @error('annee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Diffusion</h3>
        </div>
        <div class="card-body">
            <p class="text-muted small">Choisissez qui peut voir ce manuel dans son espace eleve.</p>

            <fieldset class="form-group">
                <legend class="col-form-label">Niveaux cibles</legend>
                @forelse ($niveaux as $niveau)
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" id="niveau_{{ $niveau->id }}" name="niveaux[]" value="{{ $niveau->id }}"
                            @checked(in_array($niveau->id, $niveauxSelectionnes))>
                        <label class="custom-control-label" for="niveau_{{ $niveau->id }}">{{ $niveau->libelle }}</label>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Aucun niveau ne vous est attribue. Contactez un administrateur.</p>
                @endforelse
                @error('niveaux')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </fieldset>

            @if ($peutCommun)
                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" class="custom-control-input" id="est_commun" name="est_commun" value="1"
                        @checked(old('est_commun', $manuel->est_commun ?? false))>
                    <label class="custom-control-label" for="est_commun">Ressource commune (visible par tous les niveaux)</label>
                </div>
            @endif

            <fieldset class="form-group mb-0">
                <legend class="col-form-label">Statut</legend>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="statut_brouillon" class="custom-control-input" name="statut" value="brouillon"
                        @checked(old('statut', $manuel->statut ?? 'brouillon') === 'brouillon')>
                    <label class="custom-control-label" for="statut_brouillon">Brouillon (non visible des eleves)</label>
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
        </div>
        <div class="card-body">
            <p class="text-muted small">Formats acceptes : PDF ou EPUB pour le contenu, image pour la couverture.</p>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="fichier">
                        Fichier (PDF ou EPUB) @if(!isset($manuel))<span class="text-danger">*</span>@endif
                    </label>
                    <input type="file" id="fichier" name="fichier" accept=".pdf,.epub"
                        class="form-control-file @error('fichier') is-invalid @enderror">
                    @if (isset($manuel))
                        <small class="form-text text-muted">Laisser vide pour conserver le fichier actuel.</small>
                    @endif
                    @error('fichier')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="couverture">
                        Couverture (image) @if(!isset($manuel))<span class="text-danger">*</span>@endif
                    </label>
                    <input type="file" id="couverture" name="couverture" accept="image/*"
                        class="form-control-file @error('couverture') is-invalid @enderror">
                    @if (isset($manuel))
                        <small class="form-text text-muted">Laisser vide pour conserver la couverture actuelle.</small>
                    @endif
                    @error('couverture')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-end">
        <a href="{{ route('teacher.manuels.index') }}" class="btn btn-link text-muted mr-2">Annuler</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-check-circle"></i>
            {{ isset($manuel) ? 'Enregistrer les modifications' : 'Ajouter le manuel' }}
        </button>
    </div>
</div>
