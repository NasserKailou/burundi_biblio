@extends('layouts.app')

@section('contenu')
<div class="flex gap-8">
    <aside class="w-56 shrink-0">
        <nav class="space-y-1">
            <x-sidebar-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                Tableau de bord
            </x-sidebar-link>
        </nav>
    </aside>

    <div class="min-w-0 flex-1">
        @yield('admin-contenu')
    </div>
</div>
@endsection
