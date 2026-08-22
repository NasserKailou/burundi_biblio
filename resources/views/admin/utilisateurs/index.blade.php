@extends('layouts.adminlte')

@section('page-title', 'Utilisateurs')
@section('page-description', 'Gerez les comptes eleves, enseignants et administrateurs.')

@section('page-actions')
    <a href="{{ route('admin.utilisateurs.importer.form') }}" class="btn btn-outline-secondary">
        <i class="fas fa-download"></i> Importer un CSV
    </a>
    <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Ajouter un utilisateur
    </a>
@endsection

@section('adminlte-contenu')

@if (session('erreurs_import'))
    <div class="alert alert-warning bns-reveal">
        <ul class="mb-0 pl-3">
            @foreach (session('erreurs_import') as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="GET" class="form-inline card card-body bns-reveal mb-4">
    <div class="input-group mr-2 mb-2 flex-grow-1" style="min-width:220px;">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-magnifying-glass"></i></span>
        </div>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, prenom, identifiant..." class="form-control">
    </div>
    <select name="role" class="form-control mr-2 mb-2">
        <option value="">Tous les roles</option>
        @foreach (['admin', 'enseignant', 'eleve'] as $r)
            <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>
        @endforeach
    </select>
    <select name="statut" class="form-control mr-2 mb-2">
        <option value="">Tous les statuts</option>
        <option value="actif" @selected(request('statut') === 'actif')>Actif</option>
        <option value="inactif" @selected(request('statut') === 'inactif')>Inactif / en attente</option>
    </select>
    <button type="submit" class="btn btn-outline-secondary mb-2">
        <i class="fas fa-filter"></i> Filtrer
    </button>
</form>

<div class="card bns-reveal">
    <div class="card-body p-0">
    @if ($utilisateurs->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fas fa-users fa-2x mb-2"></i>
            <p class="mb-1">Aucun utilisateur ne correspond a ces criteres</p>
            <p class="small">Ajustez vos filtres ou ajoutez un nouvel utilisateur.</p>
            <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-user-plus"></i> Ajouter un utilisateur
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Identifiant</th>
                        <th>Role</th>
                        <th>Niveau</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($utilisateurs as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-primary mr-2" style="border-radius:50%;width:2rem;height:2rem;display:inline-flex;align-items:center;justify-content:center;">
                                        {{ strtoupper(mb_substr($u->prenom, 0, 1) . mb_substr($u->nom, 0, 1)) }}
                                    </span>
                                    <span class="font-weight-bold">{{ $u->nomComplet() }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $u->identifiant }}</td>
                            <td><span class="badge badge-primary">{{ ucfirst($u->role?->libelle) }}</span></td>
                            <td class="text-muted">{{ $u->niveau?->libelle ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $u->actif ? 'badge-success' : 'badge-secondary' }}">{{ $u->actif ? 'Actif' : 'En attente' }}</span>
                            </td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('admin.utilisateurs.edit', $u) }}" title="Modifier" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-pen"></i>
                                </a>
                                @if (! $u->actif)
                                    <form method="POST" action="{{ route('admin.utilisateurs.activer', $u) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" title="Valider le compte" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.utilisateurs.desactiver', $u) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" title="Desactiver" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-xmark"></i>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.utilisateurs.reinitialiser-mdp', $u) }}" data-confirm="Reinitialiser le mot de passe de cet utilisateur ?" class="d-inline">
                                    @csrf
                                    <button type="submit" title="Reinitialiser le mot de passe" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-lock"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.utilisateurs.destroy', $u) }}" data-confirm="Supprimer cet utilisateur ?" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Supprimer" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    </div>
</div>

<div class="mt-3">{{ $utilisateurs->links() }}</div>
@endsection
