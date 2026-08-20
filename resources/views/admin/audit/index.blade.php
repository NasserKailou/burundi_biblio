@extends('layouts.admin')

@section('titre', "Journaux d'audit")

@section('admin-contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Journaux d'audit</h1>

<form method="GET" class="mb-4 flex flex-wrap gap-3">
    <input type="text" name="action" value="{{ request('action') }}" placeholder="Filtrer par action..."
        class="rounded-md border border-bns-border px-3 py-2 text-sm shadow-sm">
    <button type="submit" class="rounded-md border border-bns-border px-4 py-2 text-sm font-medium text-bns-foreground hover:bg-bns-muted">Filtrer</button>
</form>

<div class="overflow-x-auto rounded-xl border border-bns-border bg-bns-card">
    <table class="min-w-full divide-y divide-bns-border text-sm">
        <thead class="bg-bns-muted">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Date</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Utilisateur</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Action</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">Cible</th>
                <th class="px-4 py-3 text-left font-medium text-bns-muted-foreground">IP</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-bns-border">
            @foreach ($logs as $log)
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 text-bns-muted-foreground">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="px-4 py-3 text-bns-foreground">{{ $log->user?->nomComplet() ?? '—' }}</td>
                    <td class="px-4 py-3"><x-badge>{{ $log->action }}</x-badge></td>
                    <td class="px-4 py-3 text-bns-muted-foreground">{{ $log->cible ?? '—' }}</td>
                    <td class="px-4 py-3 text-bns-muted-foreground">{{ $log->ip ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $logs->links() }}</div>
@endsection
