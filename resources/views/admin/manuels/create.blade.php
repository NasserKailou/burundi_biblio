@extends('layouts.admin')

@section('titre', 'Ajouter un manuel')

@section('admin-contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Ajouter un manuel</h1>

<x-card class="max-w-3xl">
    @if ($errors->any())
        <x-alert type="error" class="mb-4">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
            </ul>
        </x-alert>
    @endif
    <form method="POST" action="{{ route('admin.manuels.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.manuels._form')
    </form>
</x-card>
@endsection
