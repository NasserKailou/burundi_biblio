@php
    $niveauxSelectionnes = old('niveaux', isset($manuel) ? $manuel->niveaux->pluck('id')->all() : []);
@endphp

<div>
    <x-form-section title="Informations generales" description="Le titre et la matiere permettent aux eleves de retrouver le manuel dans le catalogue.">
        <div class="grid gap-4 sm:grid-cols-2">
            <x-input name="titre" label="Titre" required value="{{ old('titre', $manuel->titre ?? '') }}" />
            <x-select name="matiere_id" label="Matiere" required>
                <option value="">-- Choisir --</option>
                @foreach ($matieres as $matiere)
                    <option value="{{ $matiere->id }}" @selected(old('matiere_id', $manuel->matiere_id ?? null) == $matiere->id)>{{ $matiere->libelle }}</option>
                @endforeach
            </x-select>
        </div>

        <x-select name="uploader_id" label="Proprietaire (enseignant)" required>
            <option value="">-- Choisir --</option>
            @foreach ($enseignants as $enseignant)
                <option value="{{ $enseignant->id }}" @selected(old('uploader_id', $manuel->uploader_id ?? null) == $enseignant->id)>{{ $enseignant->nomComplet() }}</option>
            @endforeach
        </x-select>

        <div>
            <label for="description" class="block text-sm font-medium text-bns-foreground">Description</label>
            <textarea id="description" name="description" rows="3"
                class="mt-1 block w-full rounded-md border border-bns-border px-3 py-2 text-sm shadow-sm transition-colors focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring">{{ old('description', $manuel->description ?? '') }}</textarea>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-input name="auteur" label="Auteur (optionnel)" value="{{ old('auteur', $manuel->auteur ?? '') }}" />
            <x-input name="annee" type="number" label="Annee (optionnelle)" value="{{ old('annee', $manuel->annee ?? '') }}" />
        </div>
    </x-form-section>

    <x-form-section title="Diffusion" description="Choisissez qui peut voir ce manuel dans son espace eleve.">
        <fieldset>
            <legend class="mb-2 block text-sm font-medium text-bns-foreground">Niveaux cibles</legend>
            <div class="flex flex-wrap gap-2">
                @foreach ($niveaux as $niveau)
                    <label class="has-[:checked]:border-bns-primary has-[:checked]:bg-teal-600/10 has-[:checked]:text-bns-primary flex cursor-pointer items-center gap-2 rounded-full border border-bns-border px-3.5 py-1.5 text-sm text-bns-foreground transition-colors hover:bg-bns-muted">
                        <input type="checkbox" name="niveaux[]" value="{{ $niveau->id }}" class="sr-only"
                            @checked(in_array($niveau->id, $niveauxSelectionnes))>
                        {{ $niveau->libelle }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <label class="flex items-center gap-2 text-sm text-bns-foreground">
            <input type="checkbox" name="est_commun" value="1" class="rounded border-bns-border text-bns-primary focus:ring-bns-ring"
                @checked(old('est_commun', $manuel->est_commun ?? false))>
            Ressource commune (visible par tous les niveaux)
        </label>

        <fieldset>
            <legend class="mb-2 block text-sm font-medium text-bns-foreground">Statut</legend>
            <div class="flex gap-4 text-sm">
                <label class="flex items-center gap-2">
                    <input type="radio" name="statut" value="brouillon" class="border-bns-border text-bns-primary focus:ring-bns-ring"
                        @checked(old('statut', $manuel->statut ?? 'brouillon') === 'brouillon')>
                    Brouillon
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="statut" value="publie" class="border-bns-border text-bns-primary focus:ring-bns-ring"
                        @checked(old('statut', $manuel->statut ?? '') === 'publie')>
                    Publie
                </label>
            </div>
        </fieldset>
    </x-form-section>

    <x-form-section title="Fichiers" description="Formats acceptes : PDF ou EPUB pour le contenu, image pour la couverture." last>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="fichier" class="block text-sm font-medium text-bns-foreground">Fichier (PDF/EPUB) @if(!isset($manuel))<span class="text-bns-destructive">*</span>@endif</label>
                <div class="mt-1 flex items-center gap-2 rounded-md border border-dashed border-bns-border bg-bns-muted/50 px-3 py-3">
                    <x-icon name="download" class="h-4 w-4 shrink-0 text-bns-muted-foreground" />
                    <input type="file" id="fichier" name="fichier" accept=".pdf,.epub" class="block w-full text-sm text-bns-muted-foreground file:mr-3 file:rounded-md file:border-0 file:bg-white file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-bns-foreground file:shadow-sm">
                </div>
                @if (isset($manuel))<p class="mt-1 text-xs text-bns-muted-foreground">Laisser vide pour conserver le fichier actuel.</p>@endif
                @error('fichier')<p class="mt-1 text-sm text-bns-destructive">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="couverture" class="block text-sm font-medium text-bns-foreground">Couverture @if(!isset($manuel))<span class="text-bns-destructive">*</span>@endif</label>
                <div class="mt-1 flex items-center gap-2 rounded-md border border-dashed border-bns-border bg-bns-muted/50 px-3 py-3">
                    <x-icon name="download" class="h-4 w-4 shrink-0 text-bns-muted-foreground" />
                    <input type="file" id="couverture" name="couverture" accept="image/*" class="block w-full text-sm text-bns-muted-foreground file:mr-3 file:rounded-md file:border-0 file:bg-white file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-bns-foreground file:shadow-sm">
                </div>
                @if (isset($manuel))<p class="mt-1 text-xs text-bns-muted-foreground">Laisser vide pour conserver la couverture actuelle.</p>@endif
                @error('couverture')<p class="mt-1 text-sm text-bns-destructive">{{ $message }}</p>@enderror
            </div>
        </div>
    </x-form-section>

    <div class="flex items-center justify-end gap-3 pt-2">
        <a href="{{ route('admin.manuels.index') }}" class="text-sm font-medium text-bns-muted-foreground hover:text-bns-foreground">Annuler</a>
        <x-button variant="primary">
            <x-icon name="check-circle" class="h-4 w-4" />
            {{ isset($manuel) ? 'Enregistrer les modifications' : 'Ajouter le manuel' }}
        </x-button>
    </div>
</div>
