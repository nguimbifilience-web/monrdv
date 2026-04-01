<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClinicController extends Controller
{
    public function index()
    {
        $clinics = Clinic::withCount(['users', 'patients', 'medecins'])->latest()->get();
        return view('clinics.index', compact('clinics'));
    }

    public function users(Clinic $clinic)
    {
        $users = User::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->where('role', '!=', 'super_admin')
            ->orderByRaw("FIELD(role, 'admin', 'secretaire', 'medecin', 'patient')")
            ->orderBy('name')
            ->get();

        return view('clinics.users', compact('clinic', 'users'));
    }

    public function storeUser(Request $request, Clinic $clinic)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,secretaire',
        ]);

        $password = Str::random(8);

        User::withoutGlobalScopes()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'plain_password' => $password,
            'role' => $request->role,
            'clinic_id' => $clinic->id,
            'email_verified_at' => now(),
        ]);

        return back()->with('success', "Compte cree ! Identifiants : {$request->email} / {$password}");
    }

    public function updateUser(Request $request, Clinic $clinic, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,secretaire,medecin,patient',
        ]);

        $user->withoutGlobalScopes();
        $user->update($request->only('name', 'email', 'role'));

        return back()->with('success', "Compte de {$user->name} mis a jour.");
    }

    public function resetUserPassword(Clinic $clinic, User $user)
    {
        $password = Str::random(8);
        $user->update([
            'password' => Hash::make($password),
            'plain_password' => $password,
        ]);

        return back()->with('success', "Mot de passe reinitialise : {$user->email} / {$password}");
    }

    public function destroyUser(Clinic $clinic, User $user)
    {
        if ($user->clinic_id !== $clinic->id) {
            abort(403);
        }
        $name = $user->name;
        $user->delete();
        return back()->with('success', "Compte de {$name} supprime.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $clinic = Clinic::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('clinics.index')->with('success', "Clinique « {$clinic->name} » créée.");
    }

    public function update(Request $request, Clinic $clinic)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $clinic->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('clinics.index')->with('success', 'Clinique mise à jour.');
    }

    public function toggleActive(Clinic $clinic)
    {
        $clinic->update(['is_active' => !$clinic->is_active]);
        $status = $clinic->is_active ? 'activée' : 'désactivée';
        return redirect()->route('clinics.index')->with('success', "Clinique {$status}.");
    }

    public function destroy(Clinic $clinic)
    {
        $clinic->delete();
        return redirect()->route('clinics.index')->with('success', 'Clinique supprimée.');
    }
}
