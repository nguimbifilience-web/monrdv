<?php

use App\Http\Controllers\{
    ProfileController, DashboardController, MedecinController,
    PatientController, RendezVousController, SpecialiteController,
    AssuranceController, ConsultationController, PatientPortalController,
    MedecinPortalController, ClinicController, CompteController,
    ExportController
};
use App\Http\Controllers\SuperAdmin\{
    DashboardController as SuperAdminDashboardController,
    UserController as SuperAdminUserController,
    BillingController as SuperAdminBillingController,
    SettingController as SuperAdminSettingController,
    SecurityController as SuperAdminSecurityController
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

// =========================================================
// 6. SUPER ADMIN (hors middleware 'clinic', gère toutes les cliniques)
// =========================================================
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->group(function () {

    // --- Dashboard Super Admin (vue par clinique) ---
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('superadmin.dashboard');

    // --- Cliniques (CRUD + détail avec onglets) — noms clinics.* conservés ---
    Route::prefix('cliniques')->name('clinics.')->group(function () {
        Route::get('/', [ClinicController::class, 'index'])->name('index');
        Route::post('/', [ClinicController::class, 'store'])->name('store');
        Route::get('/{clinic}', [ClinicController::class, 'show'])->name('show');
        Route::put('/{clinic}', [ClinicController::class, 'update'])->name('update');
        Route::delete('/{clinic}', [ClinicController::class, 'destroy'])->name('destroy');
        Route::patch('/{clinic}/toggle', [ClinicController::class, 'toggleActive'])->name('toggle');
        Route::patch('/{clinic}/block', [ClinicController::class, 'block'])->name('block');
        Route::patch('/{clinic}/unblock', [ClinicController::class, 'unblock'])->name('unblock');
        Route::delete('/{clinic}/logo', [ClinicController::class, 'removeLogo'])->name('logo.destroy');

        // Gestion des users scopée à une clinique (accessible depuis détail clinique)
        Route::get('/{clinic}/utilisateurs', [ClinicController::class, 'users'])->name('users');
        Route::post('/{clinic}/utilisateurs', [ClinicController::class, 'storeUser'])->name('users.store');
        Route::put('/{clinic}/utilisateurs/{user}', [ClinicController::class, 'updateUser'])->name('users.update');
        Route::patch('/{clinic}/utilisateurs/{user}/reset', [ClinicController::class, 'resetUserPassword'])->name('users.reset');
        Route::delete('/{clinic}/utilisateurs/{user}', [ClinicController::class, 'destroyUser'])->name('users.destroy');
    });

    // --- Sections Super Admin globales ---
    Route::name('superadmin.')->group(function () {

        // Utilisateurs globaux
        Route::prefix('utilisateurs')->name('users.')->group(function () {
            Route::get('/', [SuperAdminUserController::class, 'index'])->name('index');
            Route::post('/', [SuperAdminUserController::class, 'store'])->name('store');
            Route::put('/{user}', [SuperAdminUserController::class, 'update'])->name('update');
            Route::patch('/{user}/assign', [SuperAdminUserController::class, 'assignClinic'])->name('assign');
            Route::patch('/{user}/reset', [SuperAdminUserController::class, 'resetPassword'])->name('reset');
            Route::delete('/{user}', [SuperAdminUserController::class, 'destroy'])->name('destroy');
        });

        // Facturation
        Route::prefix('facturation')->name('billing.')->group(function () {
            Route::get('/', [SuperAdminBillingController::class, 'index'])->name('index');
            Route::put('/{clinic}', [SuperAdminBillingController::class, 'updateSubscription'])->name('update');
            Route::get('/plans', [SuperAdminBillingController::class, 'plans'])->name('plans');
            Route::post('/plans', [SuperAdminBillingController::class, 'storePlan'])->name('plans.store');
            Route::put('/plans/{plan}', [SuperAdminBillingController::class, 'updatePlan'])->name('plans.update');
            Route::delete('/plans/{plan}', [SuperAdminBillingController::class, 'destroyPlan'])->name('plans.destroy');
        });

        // Paramètres globaux
        Route::prefix('parametres')->name('settings.')->group(function () {
            Route::get('/specialites', [SuperAdminSettingController::class, 'specialites'])->name('specialites');
            Route::post('/specialites', [SuperAdminSettingController::class, 'storeSpecialite'])->name('specialites.store');
            Route::put('/specialites/{specialite}', [SuperAdminSettingController::class, 'updateSpecialite'])->name('specialites.update');
            Route::delete('/specialites/{specialite}', [SuperAdminSettingController::class, 'destroySpecialite'])->name('specialites.destroy');

            Route::get('/assurances', [SuperAdminSettingController::class, 'assurances'])->name('assurances');
            Route::post('/assurances', [SuperAdminSettingController::class, 'storeAssurance'])->name('assurances.store');
            Route::put('/assurances/{assurance}', [SuperAdminSettingController::class, 'updateAssurance'])->name('assurances.update');
            Route::delete('/assurances/{assurance}', [SuperAdminSettingController::class, 'destroyAssurance'])->name('assurances.destroy');

            Route::get('/systeme', [SuperAdminSettingController::class, 'system'])->name('system');
            Route::put('/systeme', [SuperAdminSettingController::class, 'updateSystem'])->name('system.update');

            Route::get('/apparence', [SuperAdminSettingController::class, 'appearance'])->name('appearance');
            Route::put('/apparence', [SuperAdminSettingController::class, 'updateAppearance'])->name('appearance.update');
        });

        // Sécurité
        Route::prefix('securite')->name('security.')->group(function () {
            Route::get('/logs', [SuperAdminSecurityController::class, 'logs'])->name('logs');
            Route::get('/acces', [SuperAdminSecurityController::class, 'access'])->name('access');
        });
    });
});    