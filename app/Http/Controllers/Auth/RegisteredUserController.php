<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View
    {
        $clinic = null;
        if ($request->has('clinic')) {
            $clinic = Clinic::where('slug', $request->input('clinic'))->where('is_active', true)->first();
        }

        // Sans clinique, rediriger vers login avec message
        if (!$clinic) {
            return view('auth.register', ['clinic' => null, 'noClinic' => true]);
        }

        return view('auth.register', compact('clinic'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'clinic_slug' => ['nullable', 'string', 'exists:clinics,slug'],
        ]);

        // Déterminer la clinique (obligatoire)
        $clinicId = null;
        if ($request->filled('clinic_slug')) {
            $clinic = Clinic::where('slug', $request->clinic_slug)->where('is_active', true)->first();
            $clinicId = $clinic?->id;
        }

        if (!$clinicId) {
            return back()->withInput()->withErrors(['clinic_slug' => 'Vous devez vous inscrire via le lien de votre clinique.']);
        }

        $user = User::create([
            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'patient',
            'clinic_id' => $clinicId,
        ]);

        Patient::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'user_id' => $user->id,
            'clinic_id' => $clinicId,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('patient.dashboard'));
    }
}
