<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\User;
use App\Http\Requests\StoreClinicRequest;
use App\Http\Requests\UpdateClinicRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            'role' => $request->role,
            'clinic_id' => $clinic->id,
            'email_verified_at' => now(),
        ]);

        return back()->with('success', "Compte cree ! Identifiants : {$request->email} / {$password}");
    }

    public function updateUser(Request $request, Clinic $clinic, $userId)
    {
        $user = User::withoutGlobalScopes()->where('clinic_id', $clinic->id)->findOrFail($userId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,secretaire,medecin,patient',
        ]);

        $user->update($request->only('name', 'email', 'role'));

        return back()->with('success', "Compte de {$user->name} mis a jour.");
    }

    public function resetUserPassword(Request $request, Clinic $clinic, $userId)
    {
        $user = User::withoutGlobalScopes()->where('clinic_id', $clinic->id)->findOrFail($userId);

        if ($request->filled('new_password')) {
            $request->validate(['new_password' => 'string|min:8']);
            $password = $request->new_password;
        } else {
            $password = Str::random(8);
        }

        $user->update([
            'password' => Hash::make($password),
        ]);

        return back()->with('reset_password', json_encode([
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
        ]));
    }

    public function destroyUser(Clinic $clinic, $userId)
    {
        $user = User::withoutGlobalScopes()->where('clinic_id', $clinic->id)->findOrFail($userId);
        $name = $user->name;
        $user->delete();
        return back()->with('success', "Compte de {$name} supprime.");
    }

    public function store(StoreClinicRequest $request)
    {
        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'sidebar_text_color' => $request->sidebar_text_color,
        ];

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('clinics/logos', 'public');
        }

        $clinic = Clinic::create($data);

        $message = "Clinique « {$clinic->name} » créée.";

        // Créer l'admin de la clinique si renseigné
        if ($request->filled('admin_name') && $request->filled('admin_email')) {
            $password = Str::random(8);
            User::withoutGlobalScopes()->create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'clinic_id' => $clinic->id,
                'email_verified_at' => now(),
            ]);
            $message .= " Admin : {$request->admin_email} / {$password}";
        }

        return redirect()->route('clinics.index')->with('success', $message);
    }

    public function update(UpdateClinicRequest $request, Clinic $clinic)
    {
        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'sidebar_text_color' => $request->sidebar_text_color,
            'subscription_expires_at' => $request->subscription_expires_at,
        ];

        if ($request->hasFile('logo')) {
            if ($clinic->logo_path) {
                Storage::disk('public')->delete($clinic->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('clinics/logos', 'public');
        }

        $clinic->update($data);

        return redirect()->route('clinics.index')->with('success', 'Clinique mise à jour.');
    }

    public function removeLogo(Clinic $clinic)
    {
        if ($clinic->logo_path) {
            Storage::disk('public')->delete($clinic->logo_path);
            $clinic->update(['logo_path' => null]);
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Logo supprimé.');
    }

    public function block(Request $request, Clinic $clinic)
    {
        $request->validate(['blocked_reason' => 'required|string|max:500']);

        $clinic->update([
            'is_blocked' => true,
            'blocked_reason' => $request->blocked_reason,
            'blocked_at' => now(),
        ]);

        return redirect()->route('clinics.index')->with('success', "Clinique « {$clinic->name} » bloquée.");
    }

    public function unblock(Clinic $clinic)
    {
        $clinic->update([
            'is_blocked' => false,
            'blocked_reason' => null,
            'blocked_at' => null,
        ]);

        return redirect()->route('clinics.index')->with('success', "Clinique « {$clinic->name} » débloquée.");
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
