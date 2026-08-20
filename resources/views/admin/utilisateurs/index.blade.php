@extends('layouts.admin')

@section('titre', 'Utilisateurs')

@section('admin-contenu')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-heading text-2xl font-semibold text-bns-foreground">Utilisateurs</h1>
    <div class="flex gap-3">
        <a href="{{ route('admin.utilisateurs.importer.form') }}" class="inline-flex items-center rounded-md border border-bns-border bg-white px-4 py-2 text-sm font-medium text-bns-foreground hover:bg-bns-muted">
            Importer un CSV
        </a>
        <a href="{{ route('admin.utilisateurs.create') }}" class="inline-flex items-center rounded-md bg-bns-primary px-4 py-2 text-sm font-medium text-bns-on-primary hover:bg-teal-800">
            Ajouter un utilisateur
        </a>
    </div>
</div>

@if (session('erreurs_import'))
    <x-alert type="warning" class="mb-4">
        <ul class="list-inside list-disc">
            @foreach (session('erreurs_import') as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif

<form method="GET" class="mb-4 flex flex-wrap gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, prenom, identifiant..."
        class="rounded-md border border-bns-border px-3 py-2 text-sm shadow-sm focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring">
    <select name="role" class="rounded-md border border-bns-border bg-white px-3 py-2 text-sm shadow-sm">
        <option value="">Tous les roles</option>
        @foreach (['admin', 'enseignant', 'eleve'] as $r)
            <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>
        @endforeach
    </select>
    <select name="statut" class="rounded-md border border-bns-border bg-white px-3 py-2 text-sm shadow-sm">
        <option value="">Tous les statuts</option>
        <option value="actif" @selected(request('statut') === 'actif')>Actif</option>
        <option value="inactif" @selected(request('statut') === 'inactif')>Inactif / en attente</option>
    </select>
    <button type="submit" class="rounded-md border border-bns-border px-4 py-2 text-sm font-medium text-bns-foreground hover:bg-bns-muted">Filtrer</button>
</form>

<div class="overflow-x-auto rounded-xl border border-bns-border bg-bns-card">
    <table class="min-w-full divide-y divide-bns-border text-sm">
        <thead class="bg-bns-muted">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Nom</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Identifiant</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Role</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Niveau</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Statut</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-bns-border">
            @foreach ($utilisateurs as $u)
                <tr>
                    <td class="px-4 py-3 font-medium text-bns-foreground">{{ $u->nomComplet() }}</td>
                    <td class="px-4 py-3 text-bns-muted-foreground">{{ $u->identifiant }}</td>
                    <td class="px-4 py-3 text-bns-muted-foreground">{{ $u->role?->libelle }}</td>
                    <td class="px-4 py-3 text-bns-muted-foreground">{{ $u->niveau?->libelle ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <x-badge :color="$u->actif ? 'success' : 'muted'">{{ $u->actif ? 'Actif' : 'En attente' }}</x-badge>
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-right">
                        <a href="{{ route('admin.utilisateurs.edit', $u) }}" class="mr-3 font-medium text-bns-primary hover:underline">Modifier</a>
                        @if (! $u->actif)
                            <form method="POST" action="{{ route('admin.utilisateurs.activer', $u) }}" class="mr-3 inline">
                                @csrf
                                <button type="submit" class="font-medium text-bns-success hover:underline">Valider</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.utilisateurs.desactiver', $u) }}" class="mr-3 inline">
                                @csrf
                                <button type="submit" class="font-medium text-bns-muted-foreground hover:underline">Desactiver</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.utilisateurs.reinitialiser-mdp', $u) }}" class="mr-3 inline" onsubmit="return confirm('Reinitialiser le mot de passe de cet utilisateur ?');">
                            @csrf
                            <button type="submit" class="font-medium text-bns-foreground hover:underline">Reinit. MDP</button>
                        </form>
                        <form method="POST" action="{{ route('admin.utilisateurs.destroy', $u) }}" class="inline" onsubmit="return confirm('Supprimer cet utilisateur ?');">
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

<div class="mt-4">{{ $utilisateurs->links() }}</div>
@endsection
