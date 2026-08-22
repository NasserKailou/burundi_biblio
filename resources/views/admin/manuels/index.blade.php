@extends('layouts.adminlte')

@section('page-title', 'Catalogue complet')
@section('page-description', "Gerez l'ensemble des manuels disponibles sur la plateforme.")

@section('page-actions')
    <a href="{{ route('admin.manuels.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Ajouter un manuel
    </a>
@endsection

@section('adminlte-contenu')

<form method="GET" class="form-inline card card-body bns-reveal mb-4">
    <div class="input-group mr-2 mb-2 flex-grow-1" style="min-width:200px;">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-magnifying-glass"></i></span>
        </div>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un titre..." class="form-control">
    </div>
    <select name="matiere" class="form-control mr-2 mb-2">
        <option value="">Toutes les matieres</option>
        @foreach ($matieres as $matiere)
            <option value="{{ $matiere->id }}" @selected(request('matiere') == $matiere->id)>{{ $matiere->libelle }}</option>
        @endforeach
    </select>
    <select name="statut" class="form-control mr-2 mb-2">
        <option value="">Tous statuts</option>
        <option value="publie" @selected(request('statut') === 'publie')>Publie</option>
        <option value="brouillon" @selected(request('statut') === 'brouillon')>Brouillon</option>
    </select>
    <button type="submit" class="btn btn-outline-secondary mb-2">
        <i class="fas fa-filter"></i> Filtrer
    </button>
</form>

<div class="card bns-reveal">
    <div class="card-body p-0">
    @if ($manuels->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fas fa-book-open fa-2x mb-2"></i>
            <p class="mb-1">Aucun manuel ne correspond a ces criteres</p>
            <p class="small">Ajustez vos filtres ou ajoutez un nouveau manuel au catalogue.</p>
            <a href="{{ route('admin.manuels.create') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-plus"></i> Ajouter un manuel
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur / Enseignant</th>
                        <th>Matiere</th>
                        <th>Statut</th>
                        <th>Consultations</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($manuels as $manuel)
                        <tr>
                            <td class="font-weight-bold">
                                {{ $manuel->titre }}
                                @if ($manuel->est_commun)<span class="badge badge-info ml-2">Commun</span>@endif
                            </td>
                            <td class="text-muted">{{ $manuel->uploader?->nomComplet() }}</td>
                            <td class="text-muted">{{ $manuel->matiere->libelle }}</td>
                            <td>
                                <span class="badge {{ $manuel->statut === 'publie' ? 'badge-success' : 'badge-secondary' }}">{{ $manuel->statut === 'publie' ? 'Publie' : 'Brouillon' }}</span>
                            </td>
                            <td class="text-muted">{{ $manuel->consultations_count }}</td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('admin.manuels.edit', $manuel) }}" title="Modifier" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.manuels.destroy', $manuel) }}" data-confirm="Supprimer ce manuel ?" class="d-inline">
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

<div class="mt-3">{{ $manuels->links() }}</div>
@endsection
