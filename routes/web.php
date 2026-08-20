<?php

use App\Http\Controllers\Admin\AuditController as AdminAuditController;
use App\Http\Controllers\Admin\ConfigurationController as AdminConfigurationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ManuelController as AdminManuelController;
use App\Http\Controllers\Admin\MatiereController as AdminMatiereController;
use App\Http\Controllers\Admin\NiveauController as AdminNiveauController;
use App\Http\Controllers\Admin\StatsController as AdminStatsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\ConsultationController as ApiConsultationController;
use App\Http\Controllers\Api\FavoriController as ApiFavoriController;
use App\Http\Controllers\Api\ManuelController as ApiManuelController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\ManuelController as TeacherManuelController;
use App\Http\Controllers\Teacher\StatsController as TeacherStatsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.attempt');

    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])
        ->middleware('throttle:10,1')
        ->name('register.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'role:eleve'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('catalogue')->name('catalogue.')->group(function () {
        Route::get('/', [CatalogueController::class, 'index'])->name('index');
        Route::get('/{manuel}', [CatalogueController::class, 'show'])->name('show');
        Route::get('/{manuel}/couverture', [CatalogueController::class, 'couverture'])->name('couverture');
    });
});

Route::middleware('auth')->prefix('lecteur')->name('reader.')->group(function () {
    Route::get('/{manuel}', [ReaderController::class, 'show'])->name('show');
    // Le segment {nom} est ignore par le controleur (seul $manuel compte) : il sert
    // uniquement a donner a l'URL une extension .pdf/.epub, necessaire car epubjs
    // determine le type d'ouverture (archive vs repertoire) via l'extension du
    // chemin (cf. epubjs/lib/epub/epub.js) - sans extension il tente de parcourir
    // l'URL comme un repertoire d'epub "eclate" et echoue en 404.
    Route::get('/{manuel}/fichier/{nom}', [ReaderController::class, 'fichier'])->name('fichier');
});

Route::middleware(['auth', 'role:enseignant'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('manuels')->name('manuels.')->group(function () {
        Route::get('/', [TeacherManuelController::class, 'index'])->name('index');
        Route::get('/creer', [TeacherManuelController::class, 'create'])->name('create');
        Route::post('/', [TeacherManuelController::class, 'store'])->name('store');
        Route::get('/{manuel}/modifier', [TeacherManuelController::class, 'edit'])->name('edit');
        Route::put('/{manuel}', [TeacherManuelController::class, 'update'])->name('update');
        Route::delete('/{manuel}', [TeacherManuelController::class, 'destroy'])->name('destroy');
    });

    Route::get('/statistiques', [TeacherStatsController::class, 'index'])->name('statistiques.index');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('utilisateurs')->name('utilisateurs.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/creer', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/importer', [AdminUserController::class, 'formulaireImport'])->name('importer.form');
        Route::post('/importer', [AdminUserController::class, 'importer'])->name('importer');
        Route::get('/{utilisateur}/modifier', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{utilisateur}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{utilisateur}', [AdminUserController::class, 'destroy'])->name('destroy');
        Route::post('/{utilisateur}/activer', [AdminUserController::class, 'activer'])->name('activer');
        Route::post('/{utilisateur}/desactiver', [AdminUserController::class, 'desactiver'])->name('desactiver');
        Route::post('/{utilisateur}/reinitialiser-mot-de-passe', [AdminUserController::class, 'reinitialiserMotDePasse'])->name('reinitialiser-mdp');
    });

    Route::prefix('niveaux')->name('niveaux.')->group(function () {
        Route::get('/', [AdminNiveauController::class, 'index'])->name('index');
        Route::post('/', [AdminNiveauController::class, 'store'])->name('store');
        Route::put('/{niveau}', [AdminNiveauController::class, 'update'])->name('update');
        Route::delete('/{niveau}', [AdminNiveauController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('matieres')->name('matieres.')->group(function () {
        Route::get('/', [AdminMatiereController::class, 'index'])->name('index');
        Route::post('/', [AdminMatiereController::class, 'store'])->name('store');
        Route::put('/{matiere}', [AdminMatiereController::class, 'update'])->name('update');
        Route::delete('/{matiere}', [AdminMatiereController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('manuels')->name('manuels.')->group(function () {
        Route::get('/', [AdminManuelController::class, 'index'])->name('index');
        Route::get('/creer', [AdminManuelController::class, 'create'])->name('create');
        Route::post('/', [AdminManuelController::class, 'store'])->name('store');
        Route::get('/{manuel}/modifier', [AdminManuelController::class, 'edit'])->name('edit');
        Route::put('/{manuel}', [AdminManuelController::class, 'update'])->name('update');
        Route::delete('/{manuel}', [AdminManuelController::class, 'destroy'])->name('destroy');
    });

    Route::get('/configuration', [AdminConfigurationController::class, 'edit'])->name('configuration.edit');
    Route::put('/configuration', [AdminConfigurationController::class, 'update'])->name('configuration.update');

    Route::get('/audit', [AdminAuditController::class, 'index'])->name('audit.index');

    Route::get('/statistiques', [AdminStatsController::class, 'index'])->name('statistiques.index');
    Route::get('/statistiques/export', [AdminStatsController::class, 'export'])->name('statistiques.export');
});

Route::middleware(['auth', 'throttle:120,1'])->prefix('api')->name('api.')->group(function () {
    Route::get('/manuels', [ApiManuelController::class, 'index'])->name('manuels.index');
    Route::get('/manuels/{manuel}', [ApiManuelController::class, 'show'])->name('manuels.show');

    Route::post('/consultations', [ApiConsultationController::class, 'store'])->name('consultations.store');
    Route::patch('/consultations/{consultation}', [ApiConsultationController::class, 'update'])->name('consultations.update');

    Route::post('/favoris', [ApiFavoriController::class, 'store'])->name('favoris.store');
    Route::delete('/favoris/{manuel}', [ApiFavoriController::class, 'destroy'])->name('favoris.destroy');
});
