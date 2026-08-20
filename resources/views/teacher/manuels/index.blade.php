@extends('layouts.app')

@section('titre', 'Mes manuels')

@section('contenu')
<x-page-header title="Mes manuels" description="Gerez les ressources pedagogiques que vous avez publiees." icon="book-open">
    <x-slot:actions>
        <x-button variant="primary" href="{{ route('teacher.manuels.create') }}">
            <x-icon name="plus" class="h-4 w-4" /> Ajouter un manuel
        </x-button>
    </x-slot:actions>
</x-page-header>

<div class="overflow-hidden rounded-xl border border-bns-border bg-bns-card shadow-sm">
    @if ($manuels->isEmpty())
        <x-empty-state icon="book-open" title="Vous n'avez pas encore ajoute de manuel" description="Publiez votre premiere ressource pedagogique pour vos eleves.">
            <x-slot:action>
                <x-button variant="secondary" href="{{ route('teacher.manuels.create') }}">
                    <x-icon name="plus" class="h-4 w-4" /> Ajouter un manuel
                </x-button>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-bns-border text-sm">
                <thead class="bg-bns-muted/70">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Titre</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Matiere</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Niveaux</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Statut</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Consultations</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bns-border">
                    @foreach ($manuels as $manuel)
                        <tr class="transition-colors hover:bg-bns-muted/40">
                            <td class="px-4 py-3 font-medium text-bns-foreground">
                                {{ $manuel->titre }}
                                @if ($manuel->est_commun)
                                    <x-badge color="accent" class="ml-2">Commun</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-bns-muted-foreground">{{ $manuel->matiere->libelle }}</td>
                            <td class="px-4 py-3 text-bns-muted-foreground">{{ $manuel->niveaux->pluck('libelle')->join(', ') ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <x-badge :color="$manuel->statut === 'publie' ? 'success' : 'muted'">
                                    {{ $manuel->statut === 'publie' ? 'Publie' : 'Brouillon' }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-bns-muted-foreground">{{ $manuel->consultations_count }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('teacher.manuels.edit', $manuel) }}" title="Modifier" class="flex h-8 w-8 items-center justify-center rounded-md text-bns-primary transition-colors hover:bg-teal-600/10">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form method="POST" action="{{ route('teacher.manuels.destroy', $manuel) }}" data-confirm="Supprimer ce manuel ?">
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
