@extends('layouts.admin')

@section('titre', 'Importer des utilisateurs')

@section('admin-contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Importer des eleves par CSV</h1>

<x-card class="max-w-2xl">
    <p class="mb-4 text-sm text-bns-muted-foreground">
        Le fichier CSV doit contenir une ligne d'en-tete avec les colonnes :
        <code class="rounded bg-bns-muted px-1">nom,prenom,identifiant,niveau,classe</code>.
        Le mot de passe initial est genere aleatoirement (reinitialisable ensuite depuis la liste des utilisateurs).
    </p>

    @if ($errors->any())
        <x-alert type="error" class="mb-4">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <form method="POST" action="{{ route('admin.utilisateurs.importer') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label for="csv" class="block text-sm font-medium text-bns-foreground">Fichier CSV</label>
            <input type="file" id="csv" name="csv" accept=".csv,text/csv" required
                class="mt-1 block w-full text-sm text-bns-foreground file:mr-3 file:rounded-md file:border-0 file:bg-bns-muted file:px-3 file:py-1.5 file:text-sm">
        </div>
        <x-button variant="primary">Importer</x-button>
    </form>
</x-card>
@endsection
