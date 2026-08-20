@extends('layouts.app')

@section('titre', 'Mes manuels')

@section('contenu')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-heading text-2xl font-semibold text-bns-foreground">Mes manuels</h1>
    <a href="{{ route('teacher.manuels.create') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-bns-primary px-4 py-2 text-sm font-medium text-bns-on-primary hover:bg-teal-800">
        Ajouter un manuel
    </a>
</div>

@if ($manuels->isEmpty())
    <x-card>
        <p class="text-sm text-bns-muted-foreground">Vous n'avez pas encore ajoute de manuel.</p>
    </x-card>
@else
    <div class="overflow-x-auto rounded-xl border border-bns-border bg-bns-card">
        <table class="min-w-full divide-y divide-bns-border text-sm">
            <thead class="bg-bns-muted">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Titre</th>
                    <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Matiere</th>
                    <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Niveaux</th>
                    <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Statut</th>
                    <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Consultations</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-bns-border">
                @foreach ($manuels as $manuel)
                    <tr>
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
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('teacher.manuels.edit', $manuel) }}" class="mr-3 font-medium text-bns-primary hover:underline">Modifier</a>
                            <form method="POST" action="{{ route('teacher.manuels.destroy', $manuel) }}" class="inline" onsubmit="return confirm('Supprimer ce manuel ?');">
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
@endif
@endsection
