<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\SpecialiteController;
use App\Http\Controllers\AssuranceController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PatientPortalController;
use App\Http\Controllers\MedecinPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin,secretaire'])
    ->name('dashboard');

// ===== Routes Staff (Admin + Secrétaire) =====
Route::middleware(['auth', 'role:admin,secretaire'])->group(function () {

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestion des Patients
    Route::post('patients/send-code', [PatientController::class, 'sendCode'])->name('patients.send-code');
    Route::resource('patients', PatientController::class);
    Route::patch('patients/{patient}/notes', [PatientController::class, 'updateNotes'])->name('patients.update-notes');

    // Gestion des Rendez-vous
    Route::resource('rendezvous', RendezVousController::class);
    Route::patch('rendezvous/{id}/annuler', [RendezVousController::class, 'annuler'])->name('rendezvous.annuler');
    Route::patch('rendezvous/{id}/confirmer', [RendezVousController::class, 'confirmer'])->name('rendezvous.confirmer');

    // Planning
    Route::get('/planning-global', [MedecinController::class, 'schedule'])->name('medecins.schedule');

    // Consultations
    Route::get('consultations', [ConsultationController::class, 'index'])->name('consultations.index');
    Route::post('consultations', [ConsultationController::class, 'store'])->name('consultations.store');
    Route::get('consultations/recettes-mensuelles', [ConsultationController::class, 'recettesMensuelles'])->name('consultations.recettes-mensuelles');
    Route::get('consultations/{id}/ticket', [ConsultationController::class, 'ticket'])->name('consultations.ticket');

    // API AJAX Staff
    Route::get('api/medecins/search', [MedecinController::class, 'ajaxSearch'])->name('api.medecins.search');
    Route::get('api/patients/search', [PatientController::class, 'ajaxSearch'])->name('api.patients.search');
    Route::get('api/patients/{id}/info', [ConsultationController::class, 'getPatientInfo'])->name('api.patient.info');
    Route::get('api/patients/check-email', [PatientController::class, 'checkEmail'])->name('api.patients.check-email');
});

// ===== API AJAX accessible par tous les connectés =====
Route::middleware('auth')->group(function () {
    Route::get('api/rendezvous/creneaux', [RendezVousController::class, 'creneauxDisponibles'])->name('api.rendezvous.creneaux');
    Route::get('api/medecin/{id}/planning', [PatientPortalController::class, 'getDisponibilitesMedecin'])->name('api.medecin.planning');
});

// ===== Routes Admin uniquement =====
Route::middleware(['auth', 'admin'])->group(function () {

    // Gestion des Médecins
    Route::resource('medecins', MedecinController::class);
    Route::post('/dispo/toggle', [MedecinController::class, 'toggleDispo'])->name('dispo.toggle');

    // Gestion des Spécialités
    Route::resource('specialites', SpecialiteController::class)->except(['create', 'show', 'edit']);

    // Gestion des Assurances
    Route::resource('assurances', AssuranceController::class)->except(['create', 'show', 'edit']);

    // Gestion des comptes
    Route::get('comptes', [\App\Http\Controllers\CompteController::class, 'index'])->name('comptes.index');
    Route::put('comptes/{id}', [\App\Http\Controllers\CompteController::class, 'update'])->name('comptes.update');
    Route::patch('comptes/{id}/reset-password', [\App\Http\Controllers\CompteController::class, 'resetPassword'])->name('comptes.reset-password');
    Route::delete('comptes/{id}', [\App\Http\Controllers\CompteController::class, 'destroy'])->name('comptes.destroy');

    // Traçabilité
    Route::get('activites', function (Illuminate\Http\Request $request) {
        $query = \App\Models\ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('action')) $query->where('action', $request->action);
        if ($request->filled('date')) $query->whereDate('created_at', $request->date);

        $logs = $query->paginate(20)->withQueryString();
        $users = \App\Models\User::orderBy('name')->get();

        return view('activites.index', compact('logs', 'users'));
    })->name('activites.index');
});

// ===== Portail Patient =====
Route::middleware(['auth', 'role:patient'])->prefix('espace-patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/mes-rendezvous', [PatientPortalController::class, 'mesRendezvous'])->name('rendezvous');
    Route::get('/prendre-rdv', [PatientPortalController::class, 'prendreRendezvous'])->name('prendre-rdv');
    Route::post('/prendre-rdv', [PatientPortalController::class, 'storeRendezvous'])->name('store-rdv');
    Route::patch('/rendezvous/{id}/annuler', [PatientPortalController::class, 'annulerRendezvous'])->name('annuler-rdv');

    // Documents
    Route::get('/mes-documents', [PatientPortalController::class, 'mesDocuments'])->name('documents');
    Route::post('/mes-documents', [PatientPortalController::class, 'uploadDocument'])->name('documents.upload');
    Route::delete('/mes-documents/{id}', [PatientPortalController::class, 'supprimerDocument'])->name('documents.supprimer');

    // API planning médecin
    Route::get('/api/medecin/{id}/disponibilites', [PatientPortalController::class, 'getDisponibilitesMedecin'])->name('api.medecin.dispos');
});

// ===== Portail Médecin =====
Route::middleware(['auth', 'role:medecin'])->prefix('espace-medecin')->name('medecin.')->group(function () {
    Route::get('/dashboard', [MedecinPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/planning', [MedecinPortalController::class, 'planning'])->name('planning');
    Route::get('/mes-rendezvous', [MedecinPortalController::class, 'mesRendezvous'])->name('rendezvous');
    Route::get('/mes-patients', [MedecinPortalController::class, 'mesPatients'])->name('patients');
});

require __DIR__.'/auth.php';
