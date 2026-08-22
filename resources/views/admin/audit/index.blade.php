@extends('layouts.adminlte')

@section('page-title', "Journaux d'audit")
@section('page-description', 'Historique des actions sensibles effectuees sur la plateforme.')

@section('adminlte-contenu')

<form method="GET" class="form-inline card card-body bns-reveal mb-4">
    <div class="input-group mr-2 mb-2 flex-grow-1" style="min-width:220px;">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-magnifying-glass"></i></span>
        </div>
        <input type="text" name="action" value="{{ request('action') }}" placeholder="Filtrer par action..." class="form-control">
    </div>
    <button type="submit" class="btn btn-outline-secondary mb-2">
        <i class="fas fa-filter"></i> Filtrer
    </button>
</form>

<div class="card bns-reveal">
    <div class="card-body p-0">
    @if ($logs->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fas fa-shield-halved fa-2x mb-2"></i>
            <p class="mb-0">Aucune entree d'audit</p>
            <p class="small">Aucune action ne correspond a ces criteres pour le moment.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Cible</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td class="text-nowrap text-muted">
                                <i class="fas fa-clock"></i>
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td>{{ $log->user?->nomComplet() ?? '—' }}</td>
                            <td><span class="badge badge-primary">{{ $log->action }}</span></td>
                            <td class="text-muted">{{ $log->cible ?? '—' }}</td>
                            <td class="text-muted">{{ $log->ip ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    </div>
</div>

<div class="mt-3">{{ $logs->links() }}</div>
@endsection
