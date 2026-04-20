<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Patient;
use App\Models\Medecin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,secretaire,medecin,patient',
        ]);

        $password = Str::password(12);
        $nameParts = explode(' ', $request->name, 2);
        $prenom = $nameParts[0];
        $nom = $nameParts[1] ?? $prenom;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => $request->role,
            'clinic_id' => auth()->user()->clinic_id,
            'email_verified_at' => now(),
        ]);

        // Créer la fiche associée selon le rôle
        if ($request->role === 'patient') {
            Patient::create([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $request->email,
                'telephone' => '',
                'user_id' => $user->id,
                'clinic_id' => auth()->user()->clinic_id,
            ]);
        } elseif ($request->role === 'medecin') {
            Medecin::create([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $request->email,
                'user_id' => $user->id,
                'clinic_id' => auth()->user()->clinic_id,
            ]);
        }

        ActivityLog::log('creation', "Compte {$request->role} cree : {$request->email}", $user);

        return back()->with('success', "Compte créé ! Identifiants : {$request->email} / {$password}");
    }

    public function index()
    {
        $comptes = User::where('role', '!=', 'super_admin')
            ->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'secretaire' THEN 2 WHEN 'medecin' THEN 3 WHEN 'patient' THEN 4 ELSE 5 END")
            ->orderBy('name')
            ->get();

        return view('comptes.index', compact('comptes'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('clinic_id', auth()->user()->clinic_id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $oldValues = $user->only(['name', 'email']);
        $user->update($data);

        ActivityLog::log(
            'modification',
            "Compte {$user->email} modifie" . ($request->filled('password') ? ' (mot de passe)' : ''),
            $user,
            $oldValues,
            ['name' => $request->name, 'email' => $request->email]
        );

        return back()->with('success', "Compte de {$user->name} mis à jour.");
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::where('clinic_id', auth()->user()->clinic_id)->findOrFail($id);

        if ($request->filled('new_password')) {
            $request->validate(['new_password' => 'string|min:8']);
            $newPassword = $request->new_password;
        } else {
            $newPassword = Str::password(12);
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        ActivityLog::log('modification', "Mot de passe reinitialise pour {$user->email}", $user);

        return back()->with('reset_password', json_encode([
            'name' => $user->name,
            'email' => $user->email,
            'password' => $newPassword,
        ]));
    }

    public function destroy($id)
    {
        $user = User::where('clinic_id', auth()->user()->clinic_id)->findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        ActivityLog::log('suppression', "Compte supprime : {$user->email} ({$user->role})", $user);
        $user->delete();

        return back()->with('success', "Compte de {$user->name} supprimé.");
    }
}
