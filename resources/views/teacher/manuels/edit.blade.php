@extends('layouts.app')

@section('titre', 'Modifier le manuel')

@section('contenu')
<x-page-header title="Modifier le manuel" :description="$manuel->titre" icon="pencil">
    <x-slot:actions>
        <a href="{{ route('teacher.manuels.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-bns-muted-foreground hover:text-bns-foreground">
            <x-icon name="arrow-right" class="h-4 w-4 rotate-180" /> Retour a mes manuels
        </a>
    </x-slot:actions>
</x-page-header>

<x-card class="max-w-3xl !p-0">
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

        <form method="POST" action="{{ route('teacher.manuels.update', $manuel) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('teacher.manuels._form')
        </form>
    </div>
</x-card>
@endsection
