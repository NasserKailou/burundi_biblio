@extends('adminlte::page')

{{-- Lien de la marque (sidebar) : chaque tableau de bord par role a sa propre route
     nommee ; 'dashboard' (route eleve) sert de repli si l'utilisateur est deconnecte. --}}
@php
    $dashboardRoute = 'dashboard';
    if (auth()->check()) {
        $dashboardRoute = auth()->user()->isAdmin()
            ? 'admin.dashboard'
            : (auth()->user()->isEnseignant() ? 'teacher.dashboard' : 'dashboard');
    }
@endphp
@section('dashboard_url', $dashboardRoute)

@section('title', trim(($__env->yieldContent('page-title') ?: config('adminlte.title'))))

@push('css')
    @vite(['resources/css/adminlte-skin.css'])
@endpush

@section('content_header')
    @hasSection('page-title')
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 bns-reveal">
            <div>
                <h1 class="m-0">@yield('page-title')</h1>
                @hasSection('page-description')
                    <p class="text-muted mb-0">@yield('page-description')</p>
                @endif
            </div>
            @hasSection('page-actions')
                <div>@yield('page-actions')</div>
            @endif
        </div>
    @endif
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success alert-dismissible bns-reveal" role="alert" data-toast-status="success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('status') }}
        </div>
    @endif
    @if (session('erreur'))
        <div class="alert alert-danger alert-dismissible bns-reveal" role="alert" data-toast-status="error">
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('erreur') }}
        </div>
    @endif

    @yield('adminlte-contenu')
@stop

@section('footer')
    <div class="float-right d-none d-sm-inline">Bibliotheque Numerique Scolaire</div>
    <strong>&copy; {{ date('Y') }}</strong> — Usage interne a l'etablissement.
@stop

@push('js')
    @vite(['resources/js/adminlte-app.js'])
    @if (session('status') || session('erreur'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-toast-status]').forEach((el) => {
                    if (!window.toastr) return;
                    const status = el.dataset.toastStatus;
                    const text = el.textContent.trim();
                    if (status === 'success') { window.toastr.success(text); } else { window.toastr.error(text); }
                });
            });
        </script>
    @endif
    @stack('scripts')
@endpush
