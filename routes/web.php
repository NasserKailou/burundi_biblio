<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\ManuelController as ApiManuelController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\DashboardController;
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

Route::middleware(['auth', 'role:enseignant'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('/manuels', [ApiManuelController::class, 'index'])->name('manuels.index');
    Route::get('/manuels/{manuel}', [ApiManuelController::class, 'show'])->name('manuels.show');
});
