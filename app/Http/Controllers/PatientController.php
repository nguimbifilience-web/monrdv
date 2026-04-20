<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use App\Models\Medecin;
use App\Models\Assurance;
use App\Models\ActivityLog;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\GeneratedPassword;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Patient::class);

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
        $this->authorize('create', Patient::class);

        $medecins = Medecin::with('specialite')->orderBy('nom')->get();
        $assurances = Assurance::orderBy('nom')->get();
        return view('patients.create', compact('medecins', 'assurances'));
    }

    public function store(StorePatientRequest $request)
    {
        $this->authorize('create', Patient::class);

        // Créer le compte utilisateur si un email est fourni
        $userId = null;
        if ($request->filled('email')) {
            $password = GeneratedPassword::make();
            $user = User::create([
                'name' => $request->prenom . ' ' . $request->nom,
                'email' => $request->email,
                'password' => Hash::make($password),
                'must_change_password' => true,
                'role' => 'patient',
                'clinic_id' => auth()->user()->clinic_id,
                'email_verified_at' => now(),
            ]);
            $userId = $user->id;
        }

        $patient = Patient::create(array_merge(
            $request->all(),
            ['user_id' => $userId, 'clinic_id' => auth()->user()->clinic_id]
        ));

        ActivityLog::log('creation', "Patient #{$patient->id} cree", $patient);

        $message = 'Le patient a été enregistré avec succès.';
        if (isset($password)) {
            $message .= " Identifiants : {$request->email} / {$password}";
        }

        return redirect()->route('patients.index')->with('success', $message);
    }

    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);

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
        $this->authorize('update', $patient);

        $medecins = Medecin::with('specialite')->orderBy('nom')->get();
        $assurances = Assurance::orderBy('nom')->get();
        return view('patients.edit', compact('patient', 'medecins', 'assurances'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $patient->update($request->all());
        ActivityLog::log('modification', "Patient #{$patient->id} modifie", $patient);

        return redirect()->route('patients.index')
                         ->with('success', 'Dossier patient mis à jour.');
    }

    public function destroy(Patient $patient)
    {
        $this->authorize('delete', $patient);

        ActivityLog::log('suppression', "Patient #{$patient->id} supprime", $patient);
        $patient->delete();
        return redirect()->route('patients.index')
                         ->with('success', 'Patient supprimé.');
    }

    public function updateNotes(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $data = $request->validate([
            'notes_medicales' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $patient->update($data);
        ActivityLog::log('modification', "Notes medicales mises a jour pour patient #{$patient->id}", $patient);

        return back()->with('success', 'Dossier médical mis à jour.');
    }

    public function ajaxSearch(Request $request)
    {
        $this->authorize('viewAny', Patient::class);

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
