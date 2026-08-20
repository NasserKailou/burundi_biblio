@extends('layouts.admin')

@section('titre', 'Configuration')

@section('admin-contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Configuration systeme</h1>

<x-card class="max-w-2xl">
    @if ($errors->any())
        <x-alert type="error" class="mb-4">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
            </ul>
        </x-alert>
    @endif

    <form method="POST" action="{{ route('admin.configuration.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <x-input name="etablissement_nom" label="Nom de l'etablissement" required value="{{ old('etablissement_nom', $parametres['etablissement_nom']) }}" />
        <x-input name="etablissement_logo" label="URL du logo (optionnel)" value="{{ old('etablissement_logo', $parametres['etablissement_logo']) }}" />

        <div class="grid grid-cols-2 gap-4">
            <x-input name="taille_max_fichier_mo" type="number" label="Taille max des fichiers (Mo)" required value="{{ old('taille_max_fichier_mo', $parametres['taille_max_fichier_mo']) }}" />
            <x-input name="politique_mdp_longueur_min" type="number" label="Longueur min. du mot de passe" required value="{{ old('politique_mdp_longueur_min', $parametres['politique_mdp_longueur_min']) }}" />
        </div>

        <x-input name="formats_autorises" label="Formats autorises (separes par virgule)" required value="{{ old('formats_autorises', $parametres['formats_autorises']) }}" />
        <x-input name="duree_conservation_consultations_jours" type="number" label="Duree de conservation des consultations (jours)" required value="{{ old('duree_conservation_consultations_jours', $parametres['duree_conservation_consultations_jours']) }}" />

        <label class="flex items-center gap-2 text-sm text-bns-foreground">
            <input type="checkbox" name="validation_auto" value="1" class="rounded border-bns-border text-bns-primary focus:ring-bns-ring"
                @checked(old('validation_auto', $parametres['validation_auto'] === 'true'))>
            Valider automatiquement les nouvelles inscriptions eleves (sinon validation manuelle par un administrateur)
        </label>

        <x-button variant="primary">Enregistrer</x-button>
    </form>
</x-card>
@endsection
