@php
    $niveauxGeresSelectionnes = old('niveaux_geres', isset($utilisateur) ? $utilisateur->niveauxEnseignes->pluck('id')->all() : []);
@endphp

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Identite</h3>
            <p class="text-muted small mb-0">Identifiants utilises pour la connexion a la plateforme.</p>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="prenom">Prenom</label>
                    <input type="text" id="prenom" name="prenom" required value="{{ old('prenom', $utilisateur->prenom ?? '') }}" class="form-control @error('prenom') is-invalid @enderror">
                    @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group col-sm-6">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required value="{{ old('nom', $utilisateur->nom ?? '') }}" class="form-control @error('nom') is-invalid @enderror">
                    @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="identifiant">Identifiant</label>
                    <input type="text" id="identifiant" name="identifiant" required value="{{ old('identifiant', $utilisateur->identifiant ?? '') }}" class="form-control @error('identifiant') is-invalid @enderror">
                    @error('identifiant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group col-sm-6">
                    <label for="email">Email (optionnel)</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $utilisateur->email ?? '') }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label for="password">{{ isset($utilisateur) ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe' }}</label>
                <input type="password" id="password" name="password" @if(!isset($utilisateur)) required @endif class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Role et affectation</h3>
            <p class="text-muted small mb-0">Determine les droits d'acces et le contenu visible pour ce compte.</p>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="role_id">Role</label>
                <select id="role_id" name="role_id" required class="form-control @error('role_id') is-invalid @enderror">
                    <option value="">-- Choisir --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected(old('role_id', $utilisateur->role_id ?? null) == $role->id)>{{ ucfirst($role->libelle) }}</option>
                    @endforeach
                </select>
                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="niveau_id">Niveau principal (eleve/enseignant)</label>
                    <select id="niveau_id" name="niveau_id" class="form-control @error('niveau_id') is-invalid @enderror">
                        <option value="">-- Aucun --</option>
                        @foreach ($niveaux as $niveau)
                            <option value="{{ $niveau->id }}" @selected(old('niveau_id', $utilisateur->niveau_id ?? null) == $niveau->id)>{{ $niveau->libelle }}</option>
                        @endforeach
                    </select>
                    @error('niveau_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group col-sm-6">
                    <label for="classe">Classe (optionnel)</label>
                    <input type="text" id="classe" name="classe" value="{{ old('classe', $utilisateur->classe ?? '') }}" class="form-control @error('classe') is-invalid @enderror">
                    @error('classe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <fieldset class="form-group">
                <legend class="col-form-label">Niveaux geres additionnels (enseignant multi-niveaux)</legend>
                <div>
                    @foreach ($niveaux as $niveau)
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="niveau_gere_{{ $niveau->id }}" name="niveaux_geres[]" value="{{ $niveau->id }}"
                                @checked(in_array($niveau->id, $niveauxGeresSelectionnes))>
                            <label class="custom-control-label" for="niveau_gere_{{ $niveau->id }}">{{ $niveau->libelle }}</label>
                        </div>
                    @endforeach
                </div>
            </fieldset>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Permissions</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="actif" name="actif" value="1"
                        @checked(old('actif', $utilisateur->actif ?? true))>
                    <label class="custom-control-label" for="actif">Compte actif</label>
                </div>
            </div>
            <div class="form-group mb-0">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="peut_publier_commun" name="peut_publier_commun" value="1"
                        @checked(old('peut_publier_commun', $utilisateur->peut_publier_commun ?? false))>
                    <label class="custom-control-label" for="peut_publier_commun">Droit de publier des ressources "communes" (enseignant)</label>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-end mt-3">
        <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-link text-muted mr-2">Annuler</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-check-circle"></i>
            {{ isset($utilisateur) ? 'Enregistrer les modifications' : "Creer l'utilisateur" }}
        </button>
    </div>
</div>
