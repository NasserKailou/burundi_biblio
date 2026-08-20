@extends('layouts.admin')

@section('titre', 'Niveaux')

@section('admin-contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Niveaux</h1>

<x-card class="mb-6 max-w-xl">
    <h2 class="mb-3 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Ajouter un niveau</h2>
    @if ($errors->any())
        <x-alert type="error" class="mb-4">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
            </ul>
        </x-alert>
    @endif
    <form method="POST" action="{{ route('admin.niveaux.store') }}" class="flex items-end gap-3">
        @csrf
        <div class="flex-1"><x-input name="libelle" label="Libelle" required value="{{ old('libelle') }}" /></div>
        <div class="w-28"><x-input name="ordre" type="number" label="Ordre" required value="{{ old('ordre', 0) }}" /></div>
        <x-button variant="primary">Ajouter</x-button>
    </form>
</x-card>

<div class="overflow-x-auto rounded-xl border border-bns-border bg-bns-card">
    <table class="min-w-full divide-y divide-bns-border text-sm">
        <thead class="bg-bns-muted">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Libelle</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Ordre</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Eleves</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Manuels</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-bns-border">
            @foreach ($niveaux as $niveau)
                <tr>
                    <td class="px-4 py-2">
                        <input type="text" name="libelle" value="{{ $niveau->libelle }}" form="niveau-form-{{ $niveau->id }}" class="w-40 rounded-md border border-bns-border px-2 py-1 text-sm">
                    </td>
                    <td class="px-4 py-2">
                        <input type="number" name="ordre" value="{{ $niveau->ordre }}" form="niveau-form-{{ $niveau->id }}" class="w-20 rounded-md border border-bns-border px-2 py-1 text-sm">
                    </td>
                    <td class="px-4 py-2 text-bns-muted-foreground">{{ $niveau->eleves_count }}</td>
                    <td class="px-4 py-2 text-bns-muted-foreground">{{ $niveau->manuels_count }}</td>
                    <td class="whitespace-nowrap px-4 py-2 text-right">
                        <form id="niveau-form-{{ $niveau->id }}" method="POST" action="{{ route('admin.niveaux.update', $niveau) }}" class="inline">
                            @csrf
                            @method('PUT')
                        </form>
                        <button type="submit" form="niveau-form-{{ $niveau->id }}" class="mr-3 font-medium text-bns-primary hover:underline">Enregistrer</button>
                        <form method="POST" action="{{ route('admin.niveaux.destroy', $niveau) }}" class="inline" onsubmit="return confirm('Supprimer ce niveau ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-bns-destructive hover:underline">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
