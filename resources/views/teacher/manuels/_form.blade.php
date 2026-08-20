@php
    $niveauxSelectionnes = old('niveaux', isset($manuel) ? $manuel->niveaux->pluck('id')->all() : []);
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4">
        <x-input name="titre" label="Titre" required value="{{ old('titre', $manuel->titre ?? '') }}" />
        <x-select name="matiere_id" label="Matiere" required>
            <option value="">-- Choisir --</option>
            @foreach ($matieres as $matiere)
                <option value="{{ $matiere->id }}" @selected(old('matiere_id', $manuel->matiere_id ?? null) == $matiere->id)>{{ $matiere->libelle }}</option>
            @endforeach
        </x-select>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-bns-foreground">Description</label>
        <textarea id="description" name="description" rows="3"
            class="mt-1 block w-full rounded-md border border-bns-border px-3 py-2 text-sm shadow-sm focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring">{{ old('description', $manuel->description ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <x-input name="auteur" label="Auteur (optionnel)" value="{{ old('auteur', $manuel->auteur ?? '') }}" />
        <x-input name="annee" type="number" label="Annee (optionnelle)" value="{{ old('annee', $manuel->annee ?? '') }}" />
    </div>

    <fieldset>
        <legend class="block text-sm font-medium text-bns-foreground">Niveaux cibles</legend>
        <div class="mt-2 flex flex-wrap gap-3">
            @forelse ($niveaux as $niveau)
                <label class="flex items-center gap-2 rounded-md border border-bns-border px-3 py-1.5 text-sm">
                    <input type="checkbox" name="niveaux[]" value="{{ $niveau->id }}" class="rounded border-bns-border text-bns-primary focus:ring-bns-ring"
                        @checked(in_array($niveau->id, $niveauxSelectionnes))>
                    {{ $niveau->libelle }}
                </label>
            @empty
                <p class="text-sm text-bns-muted-foreground">Aucun niveau ne vous est attribue. Contactez un administrateur.</p>
            @endforelse
        </div>
        @error('niveaux')<p class="mt-1 text-sm text-bns-destructive">{{ $message }}</p>@enderror
    </fieldset>

    @if ($peutCommun)
        <label class="flex items-center gap-2 text-sm text-bns-foreground">
            <input type="checkbox" name="est_commun" value="1" class="rounded border-bns-border text-bns-primary focus:ring-bns-ring"
                @checked(old('est_commun', $manuel->est_commun ?? false))>
            Ressource commune (visible par tous les niveaux)
        </label>
    @endif

    <fieldset>
        <legend class="block text-sm font-medium text-bns-foreground">Statut</legend>
        <div class="mt-2 flex gap-4 text-sm">
            <label class="flex items-center gap-2">
                <input type="radio" name="statut" value="brouillon" class="border-bns-border text-bns-primary focus:ring-bns-ring"
                    @checked(old('statut', $manuel->statut ?? 'brouillon') === 'brouillon')>
                Brouillon (non visible des eleves)
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" name="statut" value="publie" class="border-bns-border text-bns-primary focus:ring-bns-ring"
                    @checked(old('statut', $manuel->statut ?? '') === 'publie')>
                Publie
            </label>
        </div>
    </fieldset>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="fichier" class="block text-sm font-medium text-bns-foreground">
                Fichier (PDF ou EPUB) @if(!isset($manuel))<span class="text-bns-destructive">*</span>@endif
            </label>
            <input type="file" id="fichier" name="fichier" accept=".pdf,.epub"
                class="mt-1 block w-full text-sm text-bns-foreground file:mr-3 file:rounded-md file:border-0 file:bg-bns-muted file:px-3 file:py-1.5 file:text-sm">
            @if (isset($manuel))
                <p class="mt-1 text-xs text-bns-muted-foreground">Laisser vide pour conserver le fichier actuel.</p>
            @endif
            @error('fichier')<p class="mt-1 text-sm text-bns-destructive">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="couverture" class="block text-sm font-medium text-bns-foreground">
                Couverture (image) @if(!isset($manuel))<span class="text-bns-destructive">*</span>@endif
            </label>
            <input type="file" id="couverture" name="couverture" accept="image/*"
                class="mt-1 block w-full text-sm text-bns-foreground file:mr-3 file:rounded-md file:border-0 file:bg-bns-muted file:px-3 file:py-1.5 file:text-sm">
            @if (isset($manuel))
                <p class="mt-1 text-xs text-bns-muted-foreground">Laisser vide pour conserver la couverture actuelle.</p>
            @endif
            @error('couverture')<p class="mt-1 text-sm text-bns-destructive">{{ $message }}</p>@enderror
        </div>
    </div>

    <x-button variant="primary">{{ isset($manuel) ? 'Enregistrer les modifications' : 'Ajouter le manuel' }}</x-button>
</div>
