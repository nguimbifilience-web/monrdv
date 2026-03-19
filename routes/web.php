<?php

use Illuminate\Support\Facades\Route;
// Importation des contrôleurs
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\RendezVousController;

/*
|--------------------------------------------------------------------------
| Web Routes - Projet MonRDV
|--------------------------------------------------------------------------
*/

// 1. Page d'accueil : Redirige vers la liste des médecins
Route::get('/', function () {
    return redirect('/medecins');
});

// 2. --- ROUTES POUR LES MÉDECINS ---

// Affiche le tableau de bord avec les stats et la liste (http://localhost/medecins)
Route::get('/medecins', [MedecinController::class, 'index'])->name('medecins.index');


// 3. --- ROUTES POUR LES RENDEZ-VOUS ---

// Affiche le formulaire pour prendre un RDV (http://localhost/rendez-vous/nouveau)
Route::get('/rendez-vous/nouveau', [RendezVousController::class, 'create'])->name('rendezvous.create');

// Enregistre le RDV dans la base de données (Action du formulaire)
Route::post('/rendez-vous', [RendezVousController::class, 'store'])->name('rendezvous.store');


// 4. --- ROUTE DE TEST (À supprimer une fois le projet fini) ---
Route::get('/test-rdv', function () {
    return "<h1>Connexion établie ! Laravel fonctionne.</h1>";
});