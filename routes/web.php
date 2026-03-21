<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\RendezVousController; // <-- TRÈS IMPORTANT : Ajoute cette ligne !

// 1. Redirection de l'accueil vers le Dashboard
Route::get('/', function () {
    return redirect()->route('rendezvous.index');
});

// 2. La page de Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// 3. Le traitement du formulaire de Login
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        // Redirige vers le dashboard après connexion
        return redirect()->intended('/rendezvous'); 
    }

    return back()->withErrors(['email' => 'Identifiants incorrects.']);
})->name('login.post');

// 4. Déconnexion (Optionnel mais utile)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// 5. TOUTES les pages protégées (Admin)
Route::middleware(['auth'])->group(function () {
    
    // --- ROUTES MÉDECINS ---
    Route::get('/medecins', [MedecinController::class, 'index'])->name('medecins.index');
    Route::get('/medecins/create', [MedecinController::class, 'create'])->name('medecins.create');
    Route::post('/medecins', [MedecinController::class, 'store'])->name('medecins.store');

    // --- ROUTES RENDEZ-VOUS (Dashboard) ---
    // C'est ce qui manquait et causait la 404 !
    Route::get('/rendezvous', [RendezVousController::class, 'index'])->name('rendezvous.index');
    Route::get('/rendezvous/create', [RendezVousController::class, 'create'])->name('rendezvous.create');
    Route::post('/rendezvous', [RendezVousController::class, 'store'])->name('rendezvous.store');
});