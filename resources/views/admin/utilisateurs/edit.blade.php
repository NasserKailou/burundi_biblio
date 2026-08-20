@extends('layouts.admin')

@section('titre', 'Modifier un utilisateur')

@section('admin-contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Modifier l'utilisateur</h1>

<x-card class="max-w-2xl">
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
</x-card>
@endsection
