@extends('layouts.admin')

@section('titre', 'Utilisateurs')

@section('admin-contenu')
<x-page-header title="Utilisateurs" description="Gerez les comptes eleves, enseignants et administrateurs." icon="users">
    <x-slot:actions>
        <x-button variant="secondary" href="{{ route('admin.utilisateurs.importer.form') }}">
            <x-icon name="download" class="h-4 w-4" /> Importer un CSV
        </x-button>
        <x-button variant="primary" href="{{ route('admin.utilisateurs.create') }}">
            <x-icon name="user-plus" class="h-4 w-4" /> Ajouter un utilisateur
        </x-button>
    </x-slot:actions>
</x-page-header>

@if (session('erreurs_import'))
    <x-alert type="warning" class="mb-4">
        <ul class="list-inside list-disc">
            @foreach (session('erreurs_import') as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif

<form method="GET" class="mb-5 flex flex-wrap items-center gap-3 rounded-xl border border-bns-border bg-bns-card p-3 shadow-sm">
    <div class="relative min-w-[220px] flex-1">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bns-muted-foreground" />
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, prenom, identifiant..."
            class="w-full rounded-md border border-bns-border py-2 pl-9 pr-3 text-sm shadow-sm transition-colors focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring">
    </div>
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
    <button type="submit" class="inline-flex items-center gap-2 rounded-md border border-bns-border px-4 py-2 text-sm font-medium text-bns-foreground transition-colors hover:bg-bns-muted">
        <x-icon name="filter" class="h-4 w-4" /> Filtrer
    </button>
</form>

<div class="overflow-hidden rounded-xl border border-bns-border bg-bns-card shadow-sm">
    @if ($utilisateurs->isEmpty())
        <x-empty-state icon="users" title="Aucun utilisateur ne correspond a ces criteres" description="Ajustez vos filtres ou ajoutez un nouvel utilisateur.">
            <x-slot:action>
                <x-button variant="secondary" href="{{ route('admin.utilisateurs.create') }}">
                    <x-icon name="user-plus" class="h-4 w-4" /> Ajouter un utilisateur
                </x-button>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-bns-border text-sm">
                <thead class="bg-bns-muted/70">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Nom</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Identifiant</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Role</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Niveau</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Statut</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bns-border">
                    @foreach ($utilisateurs as $u)
                        <tr class="transition-colors hover:bg-bns-muted/40">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-teal-600/10 text-xs font-semibold text-bns-primary">
                                        {{ strtoupper(mb_substr($u->prenom, 0, 1) . mb_substr($u->nom, 0, 1)) }}
                                    </span>
                                    <span class="font-medium text-bns-foreground">{{ $u->nomComplet() }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-bns-muted-foreground">{{ $u->identifiant }}</td>
                            <td class="px-4 py-3"><x-badge color="primary">{{ ucfirst($u->role?->libelle) }}</x-badge></td>
                            <td class="px-4 py-3 text-bns-muted-foreground">{{ $u->niveau?->libelle ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <x-badge :color="$u->actif ? 'success' : 'muted'">{{ $u->actif ? 'Actif' : 'En attente' }}</x-badge>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.utilisateurs.edit', $u) }}" title="Modifier" class="flex h-8 w-8 items-center justify-center rounded-md text-bns-primary transition-colors hover:bg-teal-600/10">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    @if (! $u->actif)
                                        <form method="POST" action="{{ route('admin.utilisateurs.activer', $u) }}">
                                            @csrf
                                            <button type="submit" title="Valider le compte" class="flex h-8 w-8 items-center justify-center rounded-md text-bns-success transition-colors hover:bg-emerald-50">
                                                <x-icon name="check-circle" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.utilisateurs.desactiver', $u) }}">
                                            @csrf
                                            <button type="submit" title="Desactiver" class="flex h-8 w-8 items-center justify-center rounded-md text-bns-muted-foreground transition-colors hover:bg-bns-muted">
                                                <x-icon name="x-circle" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.utilisateurs.reinitialiser-mdp', $u) }}" data-confirm="Reinitialiser le mot de passe de cet utilisateur ?">
                                        @csrf
                                        <button type="submit" title="Reinitialiser le mot de passe" class="flex h-8 w-8 items-center justify-center rounded-md text-bns-muted-foreground transition-colors hover:bg-bns-muted">
                                            <x-icon name="lock" class="h-4 w-4" />
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.utilisateurs.destroy', $u) }}" data-confirm="Supprimer cet utilisateur ?">
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

<div class="mt-4">{{ $utilisateurs->links() }}</div>
@endsection
