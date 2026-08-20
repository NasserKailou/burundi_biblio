<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\ConsultationController as ApiConsultationController;
use App\Http\Controllers\Api\FavoriController as ApiFavoriController;
use App\Http\Controllers\Api\ManuelController as ApiManuelController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
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
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('/manuels', [ApiManuelController::class, 'index'])->name('manuels.index');
    Route::get('/manuels/{manuel}', [ApiManuelController::class, 'show'])->name('manuels.show');

    Route::post('/consultations', [ApiConsultationController::class, 'store'])->name('consultations.store');
    Route::patch('/consultations/{consultation}', [ApiConsultationController::class, 'update'])->name('consultations.update');

    Route::post('/favoris', [ApiFavoriController::class, 'store'])->name('favoris.store');
    Route::delete('/favoris/{manuel}', [ApiFavoriController::class, 'destroy'])->name('favoris.destroy');
});
