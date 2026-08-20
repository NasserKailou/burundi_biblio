@extends('layouts.admin')

@section('titre', 'Matieres')

@section('admin-contenu')
<x-page-header title="Matieres" description="Les matieres permettent de classer et filtrer les manuels du catalogue." icon="tag" />

<x-card class="mb-6 max-w-xl !p-0">
    <div class="border-b border-bns-border px-6 py-4">
        <h2 class="flex items-center gap-2 font-heading text-sm font-semibold text-bns-foreground">
            <x-icon name="plus" class="h-4 w-4 text-bns-primary" /> Ajouter une matiere
        </h2>
    </div>
    <div class="p-6">
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
    </div>
</x-card>

<div class="overflow-hidden rounded-xl border border-bns-border bg-bns-card shadow-sm">
    @if ($matieres->isEmpty())
        <x-empty-state icon="tag" title="Aucune matiere definie" description="Ajoutez une premiere matiere pour classer les manuels du catalogue." />
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-bns-border text-sm">
                <thead class="bg-bns-muted/70">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Libelle</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Manuels</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bns-border">
                    @foreach ($matieres as $matiere)
                        <tr class="transition-colors hover:bg-bns-muted/40">
                            <td class="px-4 py-2.5">
                                <input type="text" name="libelle" value="{{ $matiere->libelle }}" form="matiere-form-{{ $matiere->id }}" class="w-56 rounded-md border border-bns-border px-2.5 py-1.5 text-sm transition-colors focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring">
                            </td>
                            <td class="px-4 py-2.5"><x-badge color="primary">{{ $matiere->manuels_count }}</x-badge></td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-right">
                                <form id="matiere-form-{{ $matiere->id }}" method="POST" action="{{ route('admin.matieres.update', $matiere) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <div class="flex items-center justify-end gap-1">
                                    <button type="submit" form="matiere-form-{{ $matiere->id }}" title="Enregistrer" class="flex h-8 w-8 items-center justify-center rounded-md text-bns-primary transition-colors hover:bg-teal-600/10">
                                        <x-icon name="check-circle" class="h-4 w-4" />
                                    </button>
                                    <form method="POST" action="{{ route('admin.matieres.destroy', $matiere) }}" data-confirm="Supprimer cette matiere ?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Supprimer" class="flex h-8 w-8 items-center justify-center rounded-md text-bns-destructive transition-colors hover:bg-red-50">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
