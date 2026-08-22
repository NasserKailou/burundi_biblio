@extends('layouts.adminlte')

@section('page-title', 'Ajouter un utilisateur')
@section('page-description', 'Creez un compte eleve, enseignant ou administrateur.')

@section('page-actions')
    <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-circle-right fa-flip-horizontal"></i> Retour aux utilisateurs
    </a>
@endsection

@section('adminlte-contenu')
<div class="card bns-reveal" style="max-width:48rem;">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.utilisateurs.store') }}">
            @csrf
            @include('admin.utilisateurs._form')
        </form>
    </div>
</div>
@endsection
