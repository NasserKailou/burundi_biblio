@extends('layouts.adminlte')

@section('page-title', 'Matieres')
@section('page-description', 'Les matieres permettent de classer et filtrer les manuels du catalogue.')

@section('adminlte-contenu')

<div class="card bns-reveal mb-4" style="max-width:40rem;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-plus text-primary"></i> Ajouter une matiere</h3>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.matieres.store') }}" class="form-row align-items-end">
            @csrf
            <div class="form-group col">
                <label for="libelle">Libelle</label>
                <input type="text" id="libelle" name="libelle" required value="{{ old('libelle') }}" class="form-control @error('libelle') is-invalid @enderror">
                @error('libelle')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<div class="card bns-reveal">
    <div class="card-body p-0">
    @if ($matieres->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fas fa-tag fa-2x mb-2"></i>
            <p class="mb-0">Aucune matiere definie</p>
            <p class="small">Ajoutez une premiere matiere pour classer les manuels du catalogue.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Libelle</th>
                        <th>Manuels</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($matieres as $matiere)
                        <tr>
                            <td>
                                <input type="text" name="libelle" value="{{ $matiere->libelle }}" form="matiere-form-{{ $matiere->id }}" class="form-control form-control-sm" style="width:14rem;">
                            </td>
                            <td><span class="badge badge-primary">{{ $matiere->manuels_count }}</span></td>
                            <td class="text-right text-nowrap">
                                <form id="matiere-form-{{ $matiere->id }}" method="POST" action="{{ route('admin.matieres.update', $matiere) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <button type="submit" form="matiere-form-{{ $matiere->id }}" title="Enregistrer" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.matieres.destroy', $matiere) }}" data-confirm="Supprimer cette matiere ?" class="d-inline">
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
