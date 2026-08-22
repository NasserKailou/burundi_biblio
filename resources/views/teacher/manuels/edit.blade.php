@extends('layouts.adminlte')

@section('page-title', 'Modifier le manuel')
@section('page-description', $manuel->titre)

@section('page-actions')
    <a href="{{ route('teacher.manuels.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Retour a mes manuels
    </a>
@endsection

@section('adminlte-contenu')
<div class="row bns-reveal">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="list-inside list-disc mb-0">
                            @foreach ($errors->all() as $erreur)
                                <li>{{ $erreur }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('teacher.manuels.update', $manuel) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('teacher.manuels._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
