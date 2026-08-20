@extends('layouts.admin')

@section('titre', 'Ajouter un manuel')

@section('admin-contenu')
<x-page-header title="Ajouter un manuel" description="Publiez une nouvelle ressource pedagogique dans le catalogue." icon="book-open">
    <x-slot:actions>
        <a href="{{ route('admin.manuels.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-bns-muted-foreground hover:text-bns-foreground">
            <x-icon name="arrow-right" class="h-4 w-4 rotate-180" /> Retour au catalogue
        </a>
    </x-slot:actions>
</x-page-header>

<x-card class="max-w-3xl !p-0">
    <div class="p-6">
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
    </div>
</x-card>
@endsection
