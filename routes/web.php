<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\SpecialiteController;
use App\Http\Controllers\AssuranceController;
use App\Http\Controllers\ConsultationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
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
    Route::post('/dispo/toggle', [MedecinController::class, 'toggleDispo'])->name('dispo.toggle');
    Route::get('/planning-global', [MedecinController::class, 'schedule'])->name('medecins.schedule');

    // Gestion des Patients
    Route::resource('patients', PatientController::class);
    Route::patch('patients/{patient}/notes', [PatientController::class, 'updateNotes'])->name('patients.update-notes');

    // Gestion des Rendez-vous
    Route::resource('rendezvous', RendezVousController::class);
    Route::patch('rendezvous/{id}/annuler', [RendezVousController::class, 'annuler'])->name('rendezvous.annuler');

    // Consultations
    Route::get('consultations', [ConsultationController::class, 'index'])->name('consultations.index');
    Route::post('consultations', [ConsultationController::class, 'store'])->name('consultations.store');
    Route::get('consultations/recettes-mensuelles', [ConsultationController::class, 'recettesMensuelles'])->name('consultations.recettes-mensuelles');
    Route::get('consultations/{id}/ticket', [ConsultationController::class, 'ticket'])->name('consultations.ticket');
    Route::get('api/patients/{id}/info', [ConsultationController::class, 'getPatientInfo'])->name('api.patient.info');

    // API AJAX
    Route::get('api/medecins/search', [MedecinController::class, 'ajaxSearch'])->name('api.medecins.search');
    Route::get('api/patients/search', [PatientController::class, 'ajaxSearch'])->name('api.patients.search');
    Route::get('api/rendezvous/creneaux', [RendezVousController::class, 'creneauxDisponibles'])->name('api.rendezvous.creneaux');
    Route::get('api/patients/check-email', [PatientController::class, 'checkEmail'])->name('api.patients.check-email');

    // Traçabilité (admin uniquement)
    Route::get('activites', function (Illuminate\Http\Request $request) {
        if (!auth()->user()->is_admin) return redirect()->route('dashboard');

        $query = \App\Models\ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('action')) $query->where('action', $request->action);
        if ($request->filled('date')) $query->whereDate('created_at', $request->date);

        $logs = $query->paginate(20)->withQueryString();
        $users = \App\Models\User::orderBy('name')->get();

        return view('activites.index', compact('logs', 'users'));
    })->name('activites.index');

    // Gestion des Spécialités
    Route::resource('specialites', SpecialiteController::class)->except(['create', 'show', 'edit']);

    // Gestion des Assurances
    Route::resource('assurances', AssuranceController::class)->except(['create', 'show', 'edit']);
});

require __DIR__.'/auth.php';
