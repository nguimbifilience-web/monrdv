<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use App\Models\Clinic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): View
    {
        $clinic = null;
        if ($request->has('clinic')) {
            $clinic = Clinic::where('slug', $request->input('clinic'))->where('is_active', true)->first();
        }

        return view('auth.login', compact('clinic'));
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        ActivityLog::log('connexion', "Connexion de {$request->input('email')}", auth()->user());

        return match(auth()->user()->role) {
            'medecin' => redirect()->intended(route('medecin.dashboard')),
            'patient' => redirect()->intended(route('patient.dashboard')),
            default   => redirect()->intended(route('dashboard')),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        if ($user = $request->user()) {
            ActivityLog::log('deconnexion', "Deconnexion de {$user->email}", $user);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
