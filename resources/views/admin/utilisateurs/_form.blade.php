@php
    $niveauxGeresSelectionnes = old('niveaux_geres', isset($utilisateur) ? $utilisateur->niveauxEnseignes->pluck('id')->all() : []);
@endphp

<div>
    <x-form-section title="Identite" description="Identifiants utilises pour la connexion a la plateforme.">
        <div class="grid gap-4 sm:grid-cols-2">
            <x-input name="prenom" label="Prenom" required value="{{ old('prenom', $utilisateur->prenom ?? '') }}" />
            <x-input name="nom" label="Nom" required value="{{ old('nom', $utilisateur->nom ?? '') }}" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-input name="identifiant" label="Identifiant" required value="{{ old('identifiant', $utilisateur->identifiant ?? '') }}" />
            <x-input name="email" type="email" label="Email (optionnel)" value="{{ old('email', $utilisateur->email ?? '') }}" />
        </div>

        <x-input name="password" type="password" :label="isset($utilisateur) ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe'" :required="!isset($utilisateur)" />
    </x-form-section>

    <x-form-section title="Role et affectation" description="Determine les droits d'acces et le contenu visible pour ce compte.">
        <x-select name="role_id" label="Role" required>
            <option value="">-- Choisir --</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id', $utilisateur->role_id ?? null) == $role->id)>{{ ucfirst($role->libelle) }}</option>
            @endforeach
        </x-select>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-select name="niveau_id" label="Niveau principal (eleve/enseignant)">
                <option value="">-- Aucun --</option>
                @foreach ($niveaux as $niveau)
                    <option value="{{ $niveau->id }}" @selected(old('niveau_id', $utilisateur->niveau_id ?? null) == $niveau->id)>{{ $niveau->libelle }}</option>
                @endforeach
            </x-select>
            <x-input name="classe" label="Classe (optionnel)" value="{{ old('classe', $utilisateur->classe ?? '') }}" />
        </div>

        <fieldset>
            <legend class="mb-2 block text-sm font-medium text-bns-foreground">Niveaux geres additionnels (enseignant multi-niveaux)</legend>
            <div class="flex flex-wrap gap-2">
                @foreach ($niveaux as $niveau)
                    <label class="has-[:checked]:border-bns-primary has-[:checked]:bg-teal-600/10 has-[:checked]:text-bns-primary flex cursor-pointer items-center gap-2 rounded-full border border-bns-border px-3.5 py-1.5 text-sm text-bns-foreground transition-colors hover:bg-bns-muted">
                        <input type="checkbox" name="niveaux_geres[]" value="{{ $niveau->id }}" class="sr-only"
                            @checked(in_array($niveau->id, $niveauxGeresSelectionnes))>
                        {{ $niveau->libelle }}
                    </label>
                @endforeach
            </div>
        </fieldset>
    </x-form-section>

    <x-form-section title="Permissions" last>
        <div class="flex flex-wrap gap-6">
            <label class="flex items-center gap-2 text-sm text-bns-foreground">
                <input type="checkbox" name="actif" value="1" class="rounded border-bns-border text-bns-primary focus:ring-bns-ring"
                    @checked(old('actif', $utilisateur->actif ?? true))>
                Compte actif
            </label>
            <label class="flex items-center gap-2 text-sm text-bns-foreground">
                <input type="checkbox" name="peut_publier_commun" value="1" class="rounded border-bns-border text-bns-primary focus:ring-bns-ring"
                    @checked(old('peut_publier_commun', $utilisateur->peut_publier_commun ?? false))>
                Droit de publier des ressources "communes" (enseignant)
            </label>
        </div>
    </x-form-section>

    <div class="flex items-center justify-end gap-3 pt-2">
        <a href="{{ route('admin.utilisateurs.index') }}" class="text-sm font-medium text-bns-muted-foreground hover:text-bns-foreground">Annuler</a>
        <x-button variant="primary">
            <x-icon name="check-circle" class="h-4 w-4" />
            {{ isset($utilisateur) ? 'Enregistrer les modifications' : "Creer l'utilisateur" }}
        </x-button>
    </div>
</div>
