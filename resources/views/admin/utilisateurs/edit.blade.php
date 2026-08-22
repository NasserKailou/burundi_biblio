@extends('layouts.adminlte')

@section('page-title', "Modifier l'utilisateur")
@section('page-description', $utilisateur->nomComplet())

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
        <form method="POST" action="{{ route('admin.utilisateurs.update', $utilisateur) }}">
            @csrf
            @method('PUT')
            @include('admin.utilisateurs._form')
        </form>
    </div>
</div>
@endsection
