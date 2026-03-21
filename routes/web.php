<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\MedecinController;

// 1. La page de Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// 2. Le traitement du formulaire de Login
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/medecins');
    }

    return back()->withErrors(['email' => 'Identifiants incorrects.']);
})->name('login.post');

// 3. Les pages protégées pour l'Admin
Route::middleware(['auth'])->group(function () {
    Route::get('/medecins', [MedecinController::class, 'index'])->name('medecins.index');
    Route::get('/medecins/create', [MedecinController::class, 'create'])->name('medecins.create');
    Route::post('/medecins', [MedecinController::class, 'store'])->name('medecins.store');
});