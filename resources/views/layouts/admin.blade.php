@extends('layouts.app')

@section('contenu')
<div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-8">
    <aside class="shrink-0 lg:w-64">
        <nav class="space-y-6 rounded-xl border border-bns-border bg-bns-card p-3 shadow-sm lg:sticky lg:top-8">
            <div>
                <p class="px-3 pb-2 pt-1 text-xs font-semibold uppercase tracking-wide text-bns-muted-foreground">Pilotage</p>
                <div class="space-y-1">
                    <x-sidebar-link href="{{ route('admin.dashboard') }}" icon="grid" :active="request()->routeIs('admin.dashboard')">
                        Tableau de bord
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('admin.statistiques.index') }}" icon="chart-bar" :active="request()->routeIs('admin.statistiques.*')">
                        Statistiques
                    </x-sidebar-link>
                </div>
            </div>

            <div>
                <p class="px-3 pb-2 pt-1 text-xs font-semibold uppercase tracking-wide text-bns-muted-foreground">Gestion</p>
                <div class="space-y-1">
                    <x-sidebar-link href="{{ route('admin.utilisateurs.index') }}" icon="users" :active="request()->routeIs('admin.utilisateurs.*')">
                        Utilisateurs
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('admin.manuels.index') }}" icon="book-open" :active="request()->routeIs('admin.manuels.*')">
                        Catalogue
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('admin.niveaux.index') }}" icon="layers" :active="request()->routeIs('admin.niveaux.*')">
                        Niveaux
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('admin.matieres.index') }}" icon="tag" :active="request()->routeIs('admin.matieres.*')">
                        Matieres
                    </x-sidebar-link>
                </div>
            </div>

            <div>
                <p class="px-3 pb-2 pt-1 text-xs font-semibold uppercase tracking-wide text-bns-muted-foreground">Systeme</p>
                <div class="space-y-1">
                    <x-sidebar-link href="{{ route('admin.configuration.edit') }}" icon="cog" :active="request()->routeIs('admin.configuration.*')">
                        Configuration
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('admin.audit.index') }}" icon="shield-check" :active="request()->routeIs('admin.audit.*')">
                        Journaux d'audit
                    </x-sidebar-link>
                </div>
            </div>
        </nav>
    </aside>

    <div class="min-w-0 flex-1">
        @yield('admin-contenu')
    </div>
</div>
@endsection
