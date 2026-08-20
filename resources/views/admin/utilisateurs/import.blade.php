@extends('layouts.admin')

@section('titre', 'Importer des utilisateurs')

@section('admin-contenu')
<x-page-header title="Importer des eleves par CSV" description="Creation en masse de comptes eleves depuis un fichier CSV." icon="download">
    <x-slot:actions>
        <a href="{{ route('admin.utilisateurs.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-bns-muted-foreground hover:text-bns-foreground">
            <x-icon name="arrow-right" class="h-4 w-4 rotate-180" /> Retour aux utilisateurs
        </a>
    </x-slot:actions>
</x-page-header>

<x-card class="max-w-2xl">
    <div class="mb-4 flex items-start gap-3 rounded-lg bg-bns-muted/60 p-4 text-sm text-bns-muted-foreground">
        <x-icon name="clipboard-list" class="mt-0.5 h-4 w-4 shrink-0 text-bns-primary" />
        <p>
            Le fichier CSV doit contenir une ligne d'en-tete avec les colonnes :
            <code class="rounded bg-white px-1 py-0.5 text-bns-foreground">nom,prenom,identifiant,niveau,classe</code>.
            Le mot de passe initial est genere aleatoirement (reinitialisable ensuite depuis la liste des utilisateurs).
        </p>
    </div>

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
            <div class="mt-1 flex items-center gap-2 rounded-md border border-dashed border-bns-border bg-bns-muted/50 px-3 py-3">
                <x-icon name="download" class="h-4 w-4 shrink-0 text-bns-muted-foreground" />
                <input type="file" id="csv" name="csv" accept=".csv,text/csv" required
                    class="block w-full text-sm text-bns-muted-foreground file:mr-3 file:rounded-md file:border-0 file:bg-white file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-bns-foreground file:shadow-sm">
            </div>
        </div>
        <x-button variant="primary">
            <x-icon name="download" class="h-4 w-4" /> Importer
        </x-button>
    </form>
</x-card>
@endsection
