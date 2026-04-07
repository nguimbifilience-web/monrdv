<?php

use App\Http\Controllers\{
    ProfileController, DashboardController, MedecinController,
    PatientController, RendezVousController, SpecialiteController,
    AssuranceController, ConsultationController, PatientPortalController,
    MedecinPortalController, ClinicController, CompteController,
    ExportController
};
use Illuminate\Support\Facades\Route;

// --- Routes Publiques ---
Route::get('/', function () {
    return redirect()->route('login');
});

// Page d'accueil publique par clinique → redirige vers login avec branding
Route::get('/c/{clinic:slug}', function (\App\Models\Clinic $clinic) {
    if ($clinic->is_blocked) {
        return redirect()->route('clinic.blocked', $clinic->slug);
    }
    if (!$clinic->is_active) {
        return redirect()->route('login')->withErrors(['email' => 'Cette clinique est actuellement désactivée.']);
    }
    return redirect()->route('login', ['clinic' => $clinic->slug]);
})->name('clinic.portal');

Route::get('/clinique-suspendue/{clinic:slug}', function (\App\Models\Clinic $clinic) {
    if (!$clinic->is_blocked) {
        return redirect('/');
    }
    return view('auth.clinic-blocked', compact('clinic'));
})->name('clinic.blocked');

// --- Authentification (Breeze/Fortify) ---
require __DIR__.'/auth.php';

// =========================================================
// TOUTES LES ROUTES NÉCESSITANT AUTHENTIFICATION + CLINIQUE
// =========================================================
Route::middleware(['auth', 'clinic'])->group(function () {

    // 1. API AJAX Partagées
    Route::prefix('api')->middleware('throttle:api')->name('api.')->group(function () {
        Route::get('rendezvous/creneaux', [RendezVousController::class, 'creneauxDisponibles'])->name('rendezvous.creneaux');
        Route::get('medecin/{id}/planning', [PatientPortalController::class, 'getDisponibilitesMedecin'])->name('medecin.planning');
    });

    // Profil (accessible à tous les rôles authentifiés)
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
    });

    // 2. Espace STAFF (Admin & Secrétaire)
    Route::middleware(['role:admin,secretaire'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Patients & Rendez-vous
        Route::resource('patients', PatientController::class);
        Route::patch('patients/{patient}/notes', [PatientController::class, 'updateNotes'])->name('patients.update-notes');
        
        Route::resource('rendezvous', RendezVousController::class);
        Route::patch('rendezvous/{id}/annuler', [RendezVousController::class, 'annuler'])->name('rendezvous.annuler');
        Route::patch('rendezvous/{id}/confirmer', [RendezVousController::class, 'confirmer'])->name('rendezvous.confirmer');

        Route::get('/planning-global', [MedecinController::class, 'schedule'])->name('medecins.schedule');

        // Consultations
        Route::controller(ConsultationController::class)->group(function () {
            Route::get('consultations', 'index')->name('consultations.index');
            Route::post('consultations', 'store')->name('consultations.store');
            Route::get('consultations/recettes-mensuelles', 'recettesMensuelles')->name('consultations.recettes-mensuelles');
            Route::get('consultations/{id}/ticket', 'ticket')->name('consultations.ticket');
            Route::get('api/patients/{id}/info', 'getPatientInfo')->name('api.patient.info');
        });

        // Exports CSV
        Route::prefix('exports')->name('exports.')->group(function () {
            Route::get('patients', [ExportController::class, 'patients'])->name('patients');
            Route::get('rendezvous', [ExportController::class, 'rendezvous'])->name('rendezvous');
            Route::get('consultations', [ExportController::class, 'consultations'])->name('consultations');
            Route::get('recettes', [ExportController::class, 'recettes'])->name('recettes');
        });

        // API Staff
        Route::get('api/medecins/search', [MedecinController::class, 'ajaxSearch'])->name('api.medecins.search');
        Route::get('api/patients/search', [PatientController::class, 'ajaxSearch'])->name('api.patients.search');
        Route::get('api/patients/check-email', [PatientController::class, 'checkEmail'])->name('api.patients.check-email');
    });

    // 3. Espace ADMIN Uniquement
    Route::middleware(['admin'])->group(function () {
        Route::resource('medecins', MedecinController::class);
        Route::post('/dispo/toggle', [MedecinController::class, 'toggleDispo'])->name('dispo.toggle');
        
        Route::resource('specialites', SpecialiteController::class)->except(['create', 'show', 'edit']);
        Route::resource('assurances', AssuranceController::class)->except(['create', 'show', 'edit']);

        // Gestion Comptes
        Route::controller(CompteController::class)->group(function () {
            Route::get('comptes', 'index')->name('comptes.index');
            Route::post('comptes', 'store')->name('comptes.store');
            Route::put('comptes/{id}', 'update')->name('comptes.update');
            Route::patch('comptes/{id}/reset-password', 'resetPassword')->name('comptes.reset-password');
            Route::delete('comptes/{id}', 'destroy')->name('comptes.destroy');
        });

        Route::get('activites', [DashboardController::class, 'logs'])->name('activites.index'); // Exemple simplifié
    });

    // 4. Portail PATIENT
    Route::middleware(['role:patient'])->prefix('espace-patient')->name('patient.')->group(function () {
        Route::controller(PatientPortalController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/mes-rendezvous', 'mesRendezvous')->name('rendezvous');
            Route::get('/prendre-rdv', 'prendreRendezvous')->name('prendre-rdv');
            Route::post('/prendre-rdv', 'storeRendezvous')->middleware('throttle:patient-rdv')->name('store-rdv');
            Route::patch('/rendezvous/{id}/annuler', 'annulerRendezvous')->name('annuler-rdv');
            Route::get('/mes-documents', 'mesDocuments')->name('documents');
            Route::post('/mes-documents', 'uploadDocument')->name('documents.upload');
            Route::delete('/mes-documents/{id}', 'supprimerDocument')->name('documents.supprimer');
            Route::get('/api/medecin/{id}/disponibilites', 'getDisponibilitesMedecin')->name('api.medecin.dispos');
        });
    });

    // 5. Portail MÉDECIN
    Route::middleware(['role:medecin'])->prefix('espace-medecin')->name('medecin.')->group(function () {
        Route::controller(MedecinPortalController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/planning', 'planning')->name('planning');
            Route::get('/mes-rendezvous', 'mesRendezvous')->name('rendezvous');
            Route::get('/mes-patients', 'mesPatients')->name('patients');
        });
    });

});

// 6. SUPER ADMIN (Hors middleware 'clinic' car gère toutes les cliniques)
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('clinics.')->group(function () {
    Route::resource('cliniques', ClinicController::class)->parameters(['cliniques' => 'clinic'])->names([
        'index' => 'index',
        'create' => 'create',
        'store' => 'store',
        'show' => 'show',
        'edit' => 'edit',
        'update' => 'update',
        'destroy' => 'destroy',
    ]);
    Route::patch('/cliniques/{clinic}/toggle', [ClinicController::class, 'toggleActive'])->name('toggle');
    Route::patch('/cliniques/{clinic}/block', [ClinicController::class, 'block'])->name('block');
    Route::patch('/cliniques/{clinic}/unblock', [ClinicController::class, 'unblock'])->name('unblock');
    Route::delete('/cliniques/{clinic}/logo', [ClinicController::class, 'removeLogo'])->name('logo.destroy');

    Route::prefix('cliniques/{clinic}')->group(function () {
        Route::get('/utilisateurs', [ClinicController::class, 'users'])->name('users');
        Route::post('/utilisateurs', [ClinicController::class, 'storeUser'])->name('users.store');
        Route::put('/utilisateurs/{user}', [ClinicController::class, 'updateUser'])->name('users.update');
        Route::patch('/utilisateurs/{user}/reset', [ClinicController::class, 'resetUserPassword'])->name('users.reset');
        Route::delete('/utilisateurs/{user}', [ClinicController::class, 'destroyUser'])->name('users.destroy');
    });
});    