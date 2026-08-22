@extends('layouts.adminlte')

@section('page-title', 'Niveaux')
@section('page-description', 'Les niveaux structurent le catalogue et les comptes eleves.')

@section('adminlte-contenu')

<div class="card bns-reveal mb-4" style="max-width:40rem;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-plus text-primary"></i> Ajouter un niveau</h3>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.niveaux.store') }}" class="form-row align-items-end">
            @csrf
            <div class="form-group col">
                <label for="libelle">Libelle</label>
                <input type="text" id="libelle" name="libelle" required value="{{ old('libelle') }}" class="form-control @error('libelle') is-invalid @enderror">
                @error('libelle')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group" style="width:7rem;">
                <label for="ordre">Ordre</label>
                <input type="number" id="ordre" name="ordre" required value="{{ old('ordre', 0) }}" class="form-control @error('ordre') is-invalid @enderror">
                @error('ordre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<div class="card bns-reveal">
    <div class="card-body p-0">
    @if ($niveaux->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fas fa-layer-group fa-2x mb-2"></i>
            <p class="mb-0">Aucun niveau defini</p>
            <p class="small">Ajoutez un premier niveau pour commencer a organiser le catalogue.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Libelle</th>
                        <th>Ordre</th>
                        <th>Eleves</th>
                        <th>Manuels</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($niveaux as $niveau)
                        <tr>
                            <td>
                                <input type="text" name="libelle" value="{{ $niveau->libelle }}" form="niveau-form-{{ $niveau->id }}" class="form-control form-control-sm" style="width:10rem;">
                            </td>
                            <td>
                                <input type="number" name="ordre" value="{{ $niveau->ordre }}" form="niveau-form-{{ $niveau->id }}" class="form-control form-control-sm" style="width:6rem;">
                            </td>
                            <td><span class="badge badge-secondary">{{ $niveau->eleves_count }}</span></td>
                            <td><span class="badge badge-primary">{{ $niveau->manuels_count }}</span></td>
                            <td class="text-right text-nowrap">
                                <form id="niveau-form-{{ $niveau->id }}" method="POST" action="{{ route('admin.niveaux.update', $niveau) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <button type="submit" form="niveau-form-{{ $niveau->id }}" title="Enregistrer" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.niveaux.destroy', $niveau) }}" data-confirm="Supprimer ce niveau ?" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Supprimer" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    </div>
</div>
@endsection
