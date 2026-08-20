@extends('layouts.admin')

@section('titre', "Journaux d'audit")

@section('admin-contenu')
<x-page-header title="Journaux d'audit" description="Historique des actions sensibles effectuees sur la plateforme." icon="shield-check" />

<form method="GET" class="mb-5 flex flex-wrap items-center gap-3 rounded-xl border border-bns-border bg-bns-card p-3 shadow-sm">
    <div class="relative min-w-[220px] flex-1">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bns-muted-foreground" />
        <input type="text" name="action" value="{{ request('action') }}" placeholder="Filtrer par action..."
            class="w-full rounded-md border border-bns-border py-2 pl-9 pr-3 text-sm shadow-sm transition-colors focus:border-bns-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring">
    </div>
    <button type="submit" class="inline-flex items-center gap-2 rounded-md border border-bns-border px-4 py-2 text-sm font-medium text-bns-foreground transition-colors hover:bg-bns-muted">
        <x-icon name="filter" class="h-4 w-4" /> Filtrer
    </button>
</form>

<div class="overflow-hidden rounded-xl border border-bns-border bg-bns-card shadow-sm">
    @if ($logs->isEmpty())
        <x-empty-state icon="shield-check" title="Aucune entree d'audit" description="Aucune action ne correspond a ces criteres pour le moment." />
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-bns-border text-sm">
                <thead class="bg-bns-muted/70">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Date</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Utilisateur</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Action</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">Cible</th>
                        <th class="px-4 py-3 text-left font-semibold text-bns-muted-foreground">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bns-border">
                    @foreach ($logs as $log)
                        <tr class="transition-colors hover:bg-bns-muted/40">
                            <td class="whitespace-nowrap px-4 py-3 text-bns-muted-foreground">
                                <span class="inline-flex items-center gap-1.5">
                                    <x-icon name="clock" class="h-3.5 w-3.5 text-bns-muted-foreground" />
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-bns-foreground">{{ $log->user?->nomComplet() ?? '—' }}</td>
                            <td class="px-4 py-3"><x-badge color="primary">{{ $log->action }}</x-badge></td>
                            <td class="px-4 py-3 text-bns-muted-foreground">{{ $log->cible ?? '—' }}</td>
                            <td class="px-4 py-3 text-bns-muted-foreground">{{ $log->ip ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="mt-4">{{ $logs->links() }}</div>
@endsection
