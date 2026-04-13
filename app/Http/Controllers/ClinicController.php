<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\Specialite;
use App\Models\Assurance;
use App\Models\User;
use App\Http\Requests\StoreClinicRequest;
use App\Http\Requests\UpdateClinicRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClinicController extends Controller
{
    public function index(Request $request)
    {
        $query = Clinic::withCount(['medecins']);

        if ($search = $request->get('q')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($status = $request->get('status')) {
            match ($status) {
                'active' => $query->where('is_active', true)->where('is_blocked', false),
                'suspended' => $query->where(fn ($q) => $q->where('is_active', false)->orWhere('is_blocked', true)),
                default => null,
            };
        }
        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }

        $clinics = $query->orderBy('name')->get();
        $cities = Clinic::whereNotNull('city')->distinct()->pluck('city');

        return view('clinics.index', compact('clinics', 'cities'));
    }

    public function show(Request $request, Clinic $clinic)
    {
        $tab = $request->get('tab', 'apercu');

        $clinic->loadCount(['medecins', 'patients', 'rendezvous', 'specialites', 'assurances']);

        $data = ['clinic' => $clinic, 'tab' => $tab];

        switch ($tab) {
            case 'medecins':
                $data['medecins'] = Medecin::withoutGlobalScopes()
                    ->where('clinic_id', $clinic->id)
                    ->with('specialite')
                    ->orderBy('nom')->paginate(20)->withQueryString();
                break;
            case 'patients':
                $data['patients'] = Patient::withoutGlobalScopes()
                    ->where('clinic_id', $clinic->id)
                    ->orderBy('nom')->paginate(20)->withQueryString();
                break;
            case 'rendezvous':
                $data['rendezvous'] = RendezVous::withoutGlobalScopes()
                    ->where('clinic_id', $clinic->id)
                    ->with(['patient', 'medecin'])
                    ->orderByDesc('date_rv')->paginate(20)->withQueryString();
                break;
            case 'specialites':
                $data['specialites'] = Specialite::withoutGlobalScopes()
                    ->where('clinic_id', $clinic->id)
                    ->orderBy('nom')->get();
                break;
            case 'assurances':
                $data['assurances'] = Assurance::withoutGlobalScopes()
                    ->where('clinic_id', $clinic->id)
                    ->orderBy('nom')->get();
                break;
            case 'apercu':
            default:
                $data['revenueMonth'] = \App\Models\Consultation::withoutGlobalScopes()
                    ->where('clinic_id', $clinic->id)
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->sum('montant_patient');
                $data['rdvMonth'] = RendezVous::withoutGlobalScopes()
                    ->where('clinic_id', $clinic->id)
                    ->whereBetween('date_rv', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count();
                break;
        }

        return view('clinics.show', $data);
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
            'city' => $request->city,
            'plan_id' => $request->plan_id,
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
            'city' => $request->city,
            'plan_id' => $request->plan_id,
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

    public function destroy(Request $request, Clinic $clinic)
    {
        // Garde-fou : l'utilisateur doit retaper le nom exact de la clinique
        if ($request->filled('confirm_name') && $request->confirm_name !== $clinic->name) {
            return back()->withErrors(['confirm_name' => 'Le nom saisi ne correspond pas à la clinique.']);
        }

        $name = $clinic->name;
        $clinic->delete();
        return redirect()->route('clinics.index')->with('success', "Clinique « {$name} » supprimée.");
    }
}
