@extends('layouts.app')

@section('titre', 'Modifier le manuel')

@section('contenu')
<h1 class="mb-6 font-heading text-2xl font-semibold text-bns-foreground">Modifier le manuel</h1>

<x-card class="max-w-3xl">
    @if ($errors->any())
        <x-alert type="error" class="mb-4">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <form method="POST" action="{{ route('teacher.manuels.update', $manuel) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('teacher.manuels._form')
    </form>
</x-card>
@endsection
