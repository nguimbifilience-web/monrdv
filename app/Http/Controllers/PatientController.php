<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use App\Models\Medecin;
use App\Models\Assurance;
use App\Models\ActivityLog;
use App\Models\PatientValidationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $patients = Patient::with(['medecin', 'assurance'])
            ->filter($request->only(['search', 'medecin_id', 'est_assure', 'assurance_id']))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $medecins = Medecin::orderBy('nom')->get();
        $assurances = Assurance::orderBy('nom')->get();

        $totalPatients = Patient::count();
        $patientsAssures = Patient::where('est_assure', true)->whereNotNull('assurance_id')->count();

        return view('patients.index', compact(
            'patients', 'medecins', 'assurances',
            'totalPatients', 'patientsAssures'
        ));
    }

    public function create()
    {
        $medecins = Medecin::with('specialite')->orderBy('nom')->get();
        $assurances = Assurance::orderBy('nom')->get();
        return view('patients.create', compact('medecins', 'assurances'));
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required',
            'email' => 'nullable|email',
        ]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Invalider les anciens codes non utilisés du même utilisateur
        PatientValidationCode::where('requested_by', auth()->id())
            ->where('used', false)
            ->update(['used' => true]);

        // Créer le nouveau code en base
        PatientValidationCode::create([
            'code' => $code,
            'patient_nom' => $request->nom,
            'patient_prenom' => $request->prenom,
            'requested_by' => auth()->id(),
            'expires_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => 'Code de validation généré.',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required',
            'email' => 'nullable|email',
            'est_assure' => 'required|boolean',
            'assurance_id' => 'nullable|exists:assurances,id',
            'medecin_id' => 'nullable|exists:medecins,id',
            'validation_code' => 'required|string|size:6',
        ]);

        // Vérifier le code de validation en base
        $validCode = PatientValidationCode::where('code', $request->validation_code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$validCode) {
            return back()->withInput()->withErrors(['validation_code' => 'Code de validation invalide ou expiré.']);
        }

        // Marquer le code comme utilisé
        $validCode->update(['used' => true]);

        // Créer le compte utilisateur si un email est fourni
        $userId = null;
        if ($request->filled('email')) {
            $password = strtolower(substr($request->nom, 0, 3)) . rand(1000, 9999);
            $user = User::create([
                'name' => $request->prenom . ' ' . $request->nom,
                'email' => $request->email,
                'password' => Hash::make($password),
                'plain_password' => $password,
                'role' => 'patient',
                'email_verified_at' => now(),
            ]);
            $userId = $user->id;
        }

        $patient = Patient::create(array_merge(
            $request->except(['validation_code']),
            ['user_id' => $userId]
        ));

        ActivityLog::log('creation', "Patient créé : {$patient->nom} {$patient->prenom}", $patient);

        $message = 'Le patient a été enregistré avec succès.';
        if (isset($password)) {
            $message .= " Identifiants : {$request->email} / {$password}";
        }

        return redirect()->route('patients.index')->with('success', $message);
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'assurance',
            'medecin.specialite',
            'rendezvous' => function ($q) {
                $q->with('medecin.specialite')->orderBy('date_rv', 'desc');
            },
        ]);

        $medecinsConsultes = Medecin::with('specialite')
            ->whereHas('rendezvous', function ($q) use ($patient) {
                $q->where('patient_id', $patient->id);
            })->get();

        return view('patients.show', compact('patient', 'medecinsConsultes'));
    }

    public function edit(Patient $patient)
    {
        $medecins = Medecin::with('specialite')->orderBy('nom')->get();
        $assurances = Assurance::orderBy('nom')->get();
        return view('patients.edit', compact('patient', 'medecins', 'assurances'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required',
            'est_assure' => 'required|boolean',
            'assurance_id' => 'nullable|exists:assurances,id',
            'medecin_id' => 'nullable|exists:medecins,id',
        ]);

        $oldValues = $patient->toArray();
        $patient->update($request->all());
        ActivityLog::log('modification', "Patient modifié : {$patient->nom} {$patient->prenom}", $patient, $oldValues, $request->all());

        return redirect()->route('patients.index')
                         ->with('success', 'Dossier patient mis à jour.');
    }

    public function destroy(Patient $patient)
    {
        ActivityLog::log('suppression', "Patient supprimé : {$patient->nom} {$patient->prenom}", $patient);
        $patient->delete();
        return redirect()->route('patients.index')
                         ->with('success', 'Patient supprimé.');
    }

    public function updateNotes(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'notes_medicales' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $patient->update($data);
        ActivityLog::log('modification', "Notes médicales mises à jour pour {$patient->nom} {$patient->prenom}", $patient);

        return back()->with('success', 'Dossier médical mis à jour.');
    }

    public function ajaxSearch(Request $request)
    {
        $query = Patient::with(['medecin', 'assurance']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('medecin_id')) {
            $query->where('medecin_id', $request->medecin_id);
        }

        if ($request->filled('assurance_id')) {
            $query->where('assurance_id', $request->assurance_id);
        }

        if ($request->has('est_assure') && $request->est_assure !== '') {
            $query->where('est_assure', $request->est_assure);
        }

        return response()->json($query->latest()->limit(50)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'nom' => $p->nom,
                'prenom' => $p->prenom,
                'telephone' => $p->telephone,
                'email' => $p->email,
                'quartier' => $p->quartier,
                'est_assure' => $p->est_assure,
                'assurance_nom' => $p->assurance->nom ?? null,
                'medecin_nom' => $p->medecin ? 'Dr. ' . $p->medecin->nom : null,
                'show_url' => route('patients.show', $p->id),
                'edit_url' => route('patients.edit', $p->id),
                'delete_url' => route('patients.destroy', $p->id),
            ];
        }));
    }

    public function checkEmail(Request $request)
    {
        $exists = Patient::where('email', $request->email)
            ->when($request->filled('exclude_id'), fn($q) => $q->where('id', '!=', $request->exclude_id))
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}
