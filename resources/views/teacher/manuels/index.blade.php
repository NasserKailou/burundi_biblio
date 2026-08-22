@extends('layouts.adminlte')

@section('page-title', 'Mes manuels')
@section('page-description', 'Gerez les ressources pedagogiques que vous avez publiees.')

@section('page-actions')
    <a href="{{ route('teacher.manuels.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Ajouter un manuel
    </a>
@endsection

@section('adminlte-contenu')
<div class="card bns-reveal">
    <div class="card-body p-0">
        @if ($manuels->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="fas fa-book-open fa-2x mb-2"></i>
                <p class="mb-1">Vous n'avez pas encore ajoute de manuel</p>
                <p class="mb-3">Publiez votre premiere ressource pedagogique pour vos eleves.</p>
                <a href="{{ route('teacher.manuels.create') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-plus"></i> Ajouter un manuel
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Matiere</th>
                            <th>Niveaux</th>
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
                                    @if ($manuel->est_commun)
                                        <span class="badge badge-info ml-2">Commun</span>
                                    @endif
                                </td>
                                <td>{{ $manuel->matiere->libelle }}</td>
                                <td>{{ $manuel->niveaux->pluck('libelle')->join(', ') ?: '—' }}</td>
                                <td>
                                    <span class="badge badge-{{ $manuel->statut === 'publie' ? 'success' : 'secondary' }}">
                                        {{ $manuel->statut === 'publie' ? 'Publie' : 'Brouillon' }}
                                    </span>
                                </td>
                                <td>{{ $manuel->consultations_count }}</td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <a href="{{ route('teacher.manuels.edit', $manuel) }}" title="Modifier" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form method="POST" action="{{ route('teacher.manuels.destroy', $manuel) }}" data-confirm="Supprimer ce manuel ?" class="ml-1 d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Supprimer" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-trash"></i>
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
</div>
@endsection
