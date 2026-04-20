<?php

namespace App\Http\Controllers;

use App\Models\Ordonnance;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrdonnanceController extends Controller
{
    private function getMedecin()
    {
        $medecin = auth()->user()->medecin;
        if (!$medecin) {
            abort(403, 'Aucune fiche médecin associée à votre compte.');
        }
        return $medecin;
    }

    public function index(Request $request)
    {
        $medecin = $this->getMedecin();

        $query = Ordonnance::with(['patient', 'lignes'])
            ->where('medecin_id', $medecin->id);

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $ordonnances = $query->orderByDesc('date')->paginate(15)->withQueryString();

        return view('medecin.ordonnances.index', compact('ordonnances'));
    }

    public function create(Request $request)
    {
        $medecin = $this->getMedecin();

        $patients = Patient::whereHas('rendezvous', function ($q) use ($medecin) {
            $q->where('medecin_id', $medecin->id);
        })->orderBy('nom')->get();

        $patientId = $request->patient_id;

        return view('medecin.ordonnances.create', compact('patients', 'patientId'));
    }

    public function store(Request $request)
    {
        $medecin = $this->getMedecin();
        $clinicId = auth()->user()->clinic_id;

        $data = $request->validate([
            'patient_id' => [
                'required',
                Rule::exists('patients', 'id')->where('clinic_id', $clinicId),
            ],
            'consultation_id' => [
                'nullable',
                Rule::exists('consultations', 'id')->where('clinic_id', $clinicId),
            ],
            'date' => 'required|date',
            'notes_generales' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.medicament' => 'required|string|max:255',
            'lignes.*.posologie' => 'nullable|string|max:255',
            'lignes.*.duree' => 'nullable|string|max:100',
            'lignes.*.quantite' => 'nullable|string|max:100',
        ]);

        $ordonnance = Ordonnance::create([
            'medecin_id' => $medecin->id,
            'patient_id' => $data['patient_id'],
            'consultation_id' => $data['consultation_id'] ?? null,
            'date' => $data['date'],
            'notes_generales' => $data['notes_generales'] ?? null,
        ]);

        foreach ($data['lignes'] as $ligne) {
            $ordonnance->lignes()->create($ligne);
        }

        return redirect()->route('medecin.ordonnances.show', $ordonnance)
            ->with('success', 'Ordonnance enregistrée.');
    }

    public function show(Ordonnance $ordonnance)
    {
        $this->authorizeView($ordonnance);
        $ordonnance->load(['patient', 'medecin.specialite', 'lignes', 'clinic']);
        $view = auth()->user()->isPatient() ? 'patient.ordonnances.show' : 'medecin.ordonnances.show';
        return view($view, compact('ordonnance'));
    }

    public function print(Ordonnance $ordonnance)
    {
        $this->authorizeView($ordonnance);
        $ordonnance->load(['patient', 'medecin.specialite', 'lignes', 'clinic']);
        return view('medecin.ordonnances.print', compact('ordonnance'));
    }

    private function authorizeView(Ordonnance $ordonnance): void
    {
        $user = auth()->user();

        // Defense en profondeur : verif stricte clinic_id
        // (le ClinicScope filtre deja au route-binding, mais on garde ce check
        // au cas ou un appel ailleurs utiliserait withoutGlobalScopes)
        if ($user->clinic_id && $ordonnance->clinic_id !== $user->clinic_id) {
            abort(403);
        }

        if ($user->isMedecin() && $user->medecin && $ordonnance->medecin_id === $user->medecin->id) {
            return;
        }

        if ($user->isPatient() && $user->patient && $ordonnance->patient_id === $user->patient->id) {
            return;
        }

        abort(403);
    }
}
