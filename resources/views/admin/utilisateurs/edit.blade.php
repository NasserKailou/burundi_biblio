@extends('layouts.admin')

@section('titre', 'Modifier un utilisateur')

@section('admin-contenu')
<x-page-header title="Modifier l'utilisateur" :description="$utilisateur->nomComplet()" icon="pencil">
    <x-slot:actions>
        <a href="{{ route('admin.utilisateurs.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-bns-muted-foreground hover:text-bns-foreground">
            <x-icon name="arrow-right" class="h-4 w-4 rotate-180" /> Retour aux utilisateurs
        </a>
    </x-slot:actions>
</x-page-header>

<x-card class="max-w-2xl !p-0">
    <div class="p-6">
        @if ($errors->any())
            <x-alert type="error" class="mb-4">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif
        <form method="POST" action="{{ route('admin.utilisateurs.update', $utilisateur) }}">
            @csrf
            @method('PUT')
            @include('admin.utilisateurs._form')
        </form>
    </div>
</x-card>
@endsection
