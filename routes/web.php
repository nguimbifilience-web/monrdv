<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\SpecialiteController;
use App\Http\Controllers\AssuranceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestion des Médecins
    Route::resource('medecins', MedecinController::class);
    
    // Route pour la disponibilité (AJAX)
    Route::post('/dispo/toggle', [MedecinController::class, 'toggleDispo'])->name('dispo.toggle');

    // On garde quand même cette route si tu veux y accéder via un menu
    Route::get('/planning-global', [MedecinController::class, 'schedule'])->name('medecins.schedule');

    // Gestion des Patients
    Route::resource('patients', PatientController::class);

    // Gestion des Rendez-vous
    Route::resource('rendezvous', RendezVousController::class);

    // Gestion des Spécialités
    Route::resource('specialites', SpecialiteController::class)->except(['create', 'show', 'edit']);

    // Gestion des Assurances
    Route::resource('assurances', AssuranceController::class)->except(['create', 'show', 'edit']);
});

require __DIR__.'/auth.php';