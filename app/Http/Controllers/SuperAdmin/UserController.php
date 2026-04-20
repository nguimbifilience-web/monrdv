<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\GeneratedPassword;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withoutGlobalScopes()
            ->with('clinic')
            ->where('role', '!=', 'super_admin');

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->filled('clinic_id')) {
            if ($request->clinic_id === 'none') {
                $query->whereNull('clinic_id');
            } else {
                $query->where('clinic_id', $request->clinic_id);
            }
        }
        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();
        $clinics = Clinic::orderBy('name')->get(['id', 'name']);

        return view('superadmin.users.index', compact('users', 'clinics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,secretaire,medecin,patient',
            'clinic_id' => 'nullable|exists:clinics,id',
        ]);

        $password = GeneratedPassword::make();

        User::withoutGlobalScopes()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => $request->role,
            'clinic_id' => $request->clinic_id,
            'email_verified_at' => now(),
        ]);

        return back()->with('success', "Compte créé. Identifiants : {$request->email} / {$password}");
    }

    public function update(Request $request, $userId)
    {
        $user = User::withoutGlobalScopes()->findOrFail($userId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,secretaire,medecin,patient',
            'clinic_id' => 'nullable|exists:clinics,id',
        ]);

        $user->update($request->only('name', 'email', 'role', 'clinic_id'));
        return back()->with('success', "Compte {$user->name} mis à jour.");
    }

    public function assignClinic(Request $request, $userId)
    {
        $user = User::withoutGlobalScopes()->findOrFail($userId);
        $request->validate(['clinic_id' => 'nullable|exists:clinics,id']);
        $user->update(['clinic_id' => $request->clinic_id]);
        return back()->with('success', "Clinique assignée à {$user->name}.");
    }

    public function resetPassword(Request $request, $userId)
    {
        $user = User::withoutGlobalScopes()->findOrFail($userId);
        $password = $request->filled('new_password') ? $request->new_password : GeneratedPassword::make();
        $user->update(['password' => Hash::make($password)]);
        return back()->with('reset_password', json_encode([
            'name' => $user->name, 'email' => $user->email, 'password' => $password,
        ]));
    }

    public function destroy($userId)
    {
        $user = User::withoutGlobalScopes()->findOrFail($userId);
        $name = $user->name;
        $user->delete();
        return back()->with('success', "Compte {$name} supprimé.");
    }
}
