@extends('layouts.app')

@section('contenu')
<div class="flex gap-8">
    <aside class="w-56 shrink-0">
        <nav class="space-y-1">
            <x-sidebar-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                Tableau de bord
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.utilisateurs.index') }}" :active="request()->routeIs('admin.utilisateurs.*')">
                Utilisateurs
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.manuels.index') }}" :active="request()->routeIs('admin.manuels.*')">
                Catalogue
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.niveaux.index') }}" :active="request()->routeIs('admin.niveaux.*')">
                Niveaux
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.matieres.index') }}" :active="request()->routeIs('admin.matieres.*')">
                Matieres
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.configuration.edit') }}" :active="request()->routeIs('admin.configuration.*')">
                Configuration
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.audit.index') }}" :active="request()->routeIs('admin.audit.*')">
                Journaux d'audit
            </x-sidebar-link>
        </nav>
    </aside>

    <div class="min-w-0 flex-1">
        @yield('admin-contenu')
    </div>
</div>
@endsection
