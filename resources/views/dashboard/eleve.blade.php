@extends('layouts.adminlte')

@section('page-title', 'Bonjour ' . $user->prenom)
@section('page-description', 'Retrouvez vos lectures en cours, vos favoris et votre profil.')

@section('adminlte-contenu')

@if ($manuelsEnCours->isNotEmpty())
<section class="mb-4 bns-reveal">
    <h2 class="h5 mb-3">Reprendre la lecture</h2>
    <div class="row bns-reveal-list">
        @foreach ($manuelsEnCours as $manuel)
            <div class="col-6 col-sm-4 col-lg-3 mb-3">
                <a href="{{ route('reader.show', $manuel) }}" class="text-decoration-none">
                    <div class="card h-100">
                        <img src="{{ route('catalogue.couverture', $manuel) }}" class="card-img-top" alt="Couverture du manuel {{ $manuel->titre }}" loading="lazy">
                        <div class="card-body p-2">
                            <p class="mb-1 font-weight-bold text-dark small">{{ $manuel->titre }}</p>
                            <p class="mb-0 text-muted small">{{ $manuel->matiere->libelle }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</section>
@endif

@if ($manuelsFavoris->isNotEmpty())
<section class="mb-4 bns-reveal">
    <h2 class="h5 mb-3">Mes favoris</h2>
    <div class="row bns-reveal-list">
        @foreach ($manuelsFavoris as $manuel)
            <div class="col-6 col-sm-4 col-lg-3 mb-3">
                <a href="{{ route('catalogue.show', $manuel) }}" class="text-decoration-none">
                    <div class="card h-100">
                        <img src="{{ route('catalogue.couverture', $manuel) }}" class="card-img-top" alt="Couverture du manuel {{ $manuel->titre }}" loading="lazy">
                        <div class="card-body p-2">
                            <p class="mb-1 font-weight-bold text-dark small">{{ $manuel->titre }}</p>
                            <p class="mb-0 text-muted small">{{ $manuel->matiere->libelle }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</section>
@endif

<div class="row bns-reveal-list">
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="text-uppercase text-muted small font-weight-bold mb-3">Mon profil</h2>
                <div class="d-flex justify-content-between border-bottom py-1"><span>Niveau</span><span class="font-weight-medium">{{ $user->niveau?->libelle ?? '—' }}</span></div>
                <div class="d-flex justify-content-between border-bottom py-1"><span>Classe</span><span class="font-weight-medium">{{ $user->classe ?? '—' }}</span></div>
                <div class="d-flex justify-content-between py-1"><span>Identifiant</span><span class="font-weight-medium">{{ $user->identifiant }}</span></div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="text-uppercase text-muted small font-weight-bold mb-3">Activite</h2>
                <p class="mb-1">{{ $user->consultations()->count() }} lecture(s) enregistree(s).</p>
                <p class="mb-0">{{ $user->favoris()->count() }} favori(s).</p>
            </div>
        </div>
    </div>
</div>
@endsection
