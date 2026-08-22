@extends('layouts.adminlte')

@section('page-title', 'Modifier le manuel')
@section('page-description', $manuel->titre)

@section('page-actions')
    <a href="{{ route('admin.manuels.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-circle-right fa-flip-horizontal"></i> Retour au catalogue
    </a>
@endsection

@section('adminlte-contenu')
<div class="card bns-reveal" style="max-width:56rem;">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.manuels.update', $manuel) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.manuels._form')
        </form>
    </div>
</div>
@endsection
