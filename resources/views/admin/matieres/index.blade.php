@extends('layouts.admin')

@section('titre', 'Matieres')

@section('admin-contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Matieres</h1>

<x-card class="mb-6 max-w-xl">
    <h2 class="mb-3 text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">Ajouter une matiere</h2>
    @if ($errors->any())
        <x-alert type="error" class="mb-4">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
            </ul>
        </x-alert>
    @endif
    <form method="POST" action="{{ route('admin.matieres.store') }}" class="flex items-end gap-3">
        @csrf
        <div class="flex-1"><x-input name="libelle" label="Libelle" required value="{{ old('libelle') }}" /></div>
        <x-button variant="primary">Ajouter</x-button>
    </form>
</x-card>

<div class="overflow-x-auto rounded-xl border border-bns-border bg-bns-card">
    <table class="min-w-full divide-y divide-bns-border text-sm">
        <thead class="bg-bns-muted">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Libelle</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Manuels</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-bns-border">
            @foreach ($matieres as $matiere)
                <tr>
                    <td class="px-4 py-2">
                        <input type="text" name="libelle" value="{{ $matiere->libelle }}" form="matiere-form-{{ $matiere->id }}" class="w-56 rounded-md border border-bns-border px-2 py-1 text-sm">
                    </td>
                    <td class="px-4 py-2 text-bns-muted-foreground">{{ $matiere->manuels_count }}</td>
                    <td class="whitespace-nowrap px-4 py-2 text-right">
                        <form id="matiere-form-{{ $matiere->id }}" method="POST" action="{{ route('admin.matieres.update', $matiere) }}" class="inline">
                            @csrf
                            @method('PUT')
                        </form>
                        <button type="submit" form="matiere-form-{{ $matiere->id }}" class="mr-3 font-medium text-bns-primary hover:underline">Enregistrer</button>
                        <form method="POST" action="{{ route('admin.matieres.destroy', $matiere) }}" class="inline" data-confirm="Supprimer cette matiere ?">
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
