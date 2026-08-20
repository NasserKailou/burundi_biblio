@extends('layouts.admin')

@section('titre', 'Niveaux')

@section('admin-contenu')
<x-page-header title="Niveaux" description="Les niveaux structurent le catalogue et les comptes eleves." icon="layers" />

<x-card class="mb-6 max-w-xl !p-0">
    <div class="border-b border-bns-border px-6 py-4">
        <h2 class="flex items-center gap-2 font-heading text-sm font-semibold text-bns-foreground">
            <x-icon name="plus" class="h-4 w-4 text-bns-primary" /> Ajouter un niveau
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
        <form method="POST" action="{{ route('admin.niveaux.store') }}" class="flex items-end gap-3">
            @csrf
            <div class="flex-1"><x-input name="libelle" label="Libelle" required value="{{ old('libelle') }}" /></div>
            <div class="w-28"><x-input name="ordre" type="number" label="Ordre" required value="{{ old('ordre', 0) }}" /></div>
            <x-button variant="primary">Ajouter</x-button>
        </form>
    </div>
</x-card>

<div class="overflow-hidden rounded-xl border border-bns-border bg-bns-card shadow-sm">
    @if ($niveaux->isEmpty())
        <x-empty-state icon="layers" title="Aucun niveau defini" description="Ajoutez un premier niveau pour commencer a organiser le catalogue." />
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-bns-border text-sm">
                <thead class="bg-bns-muted/70">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Libelle</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Ordre</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Eleves</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Manuels</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bns-border">
                    @foreach ($niveaux as $niveau)
                        <tr class="transition-colors hover:bg-bns-muted/40">
                            <td class="px-4 py-2.5">
                                <input type="text" name="libelle" value="{{ $niveau->libelle }}" form="niveau-form-{{ $niveau->id }}" class="w-40 rounded-md border border-bns-border px-2.5 py-1.5 text-sm transition-colors focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring">
                            </td>
                            <td class="px-4 py-2.5">
                                <input type="number" name="ordre" value="{{ $niveau->ordre }}" form="niveau-form-{{ $niveau->id }}" class="w-20 rounded-md border border-bns-border px-2.5 py-1.5 text-sm transition-colors focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring">
                            </td>
                            <td class="px-4 py-2.5"><x-badge color="muted">{{ $niveau->eleves_count }}</x-badge></td>
                            <td class="px-4 py-2.5"><x-badge color="primary">{{ $niveau->manuels_count }}</x-badge></td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-right">
                                <form id="niveau-form-{{ $niveau->id }}" method="POST" action="{{ route('admin.niveaux.update', $niveau) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <div class="flex items-center justify-end gap-1">
                                    <button type="submit" form="niveau-form-{{ $niveau->id }}" title="Enregistrer" class="flex h-8 w-8 items-center justify-center rounded-md text-bns-primary transition-colors hover:bg-teal-600/10">
                                        <x-icon name="check-circle" class="h-4 w-4" />
                                    </button>
                                    <form method="POST" action="{{ route('admin.niveaux.destroy', $niveau) }}" data-confirm="Supprimer ce niveau ?">
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
