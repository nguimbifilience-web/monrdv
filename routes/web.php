<?php

use Illuminate\Support\Facades\Route;

// Importation des Contrôleurs
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\SpecialiteController;

/*
|--------------------------------------------------------------------------
| Web Routes - Application MonRDV
|--------------------------------------------------------------------------
*/

// Page d'accueil : Redirige directement vers le planning
Route::get('/', function () {
    return redirect()->route('rendezvous.index');
});

/**
 * ROUTES RESSOURCES (Modèle Dashboard)
 * Chaque ligne génère automatiquement les routes : index, store, show, edit, update, destroy
 */

// Gestion des Patients (Liste + Ajout)
Route::resource('patients', PatientController::class);

// Gestion des Médecins (Celle qui manquait dans ta liste !)
Route::resource('medecins', MedecinController::class);

// Gestion du Planning des Rendez-vous
Route::resource('rendezvous', RendezVousController::class);

// Gestion des Spécialités Médicales
Route::resource('specialites', SpecialiteController::class);

/**
 * ROUTES SUPPLÉMENTAIRES
 */
// Recherche rapide de patient (Optionnel pour ta Licence)
Route::get('/recherche-patient', [PatientController::class, 'search'])->name('patients.search');