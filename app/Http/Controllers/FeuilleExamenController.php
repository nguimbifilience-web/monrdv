<?php

namespace App\Http\Controllers;

use App\Models\FeuilleExamen;
use App\Models\Patient;
use Illuminate\Http\Request;

class FeuilleExamenController extends Controller
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

        $query = FeuilleExamen::with(['patient', 'lignes'])
            ->where('medecin_id', $medecin->id);

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $feuilles = $query->orderByDesc('date')->paginate(15)->withQueryString();

        return view('medecin.examens.index', compact('feuilles'));
    }

    public function create(Request $request)
    {
        $medecin = $this->getMedecin();

        $patients = Patient::whereHas('rendezvous', function ($q) use ($medecin) {
            $q->where('medecin_id', $medecin->id);
        })->orderBy('nom')->get();

        $patientId = $request->patient_id;

        return view('medecin.examens.create', compact('patients', 'patientId'));
    }

    public function store(Request $request)
    {
        $medecin = $this->getMedecin();

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'date' => 'required|date',
            'motif_clinique' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.type_examen' => 'required|in:biologie,imagerie,autre',
            'lignes.*.libelle' => 'required|string|max:255',
            'lignes.*.urgence' => 'nullable|boolean',
        ]);

        $feuille = FeuilleExamen::create([
            'medecin_id' => $medecin->id,
            'patient_id' => $data['patient_id'],
            'consultation_id' => $data['consultation_id'] ?? null,
            'date' => $data['date'],
            'motif_clinique' => $data['motif_clinique'] ?? null,
        ]);

        foreach ($data['lignes'] as $ligne) {
            $feuille->lignes()->create([
                'type_examen' => $ligne['type_examen'],
                'libelle' => $ligne['libelle'],
                'urgence' => !empty($ligne['urgence']),
            ]);
        }

        return redirect()->route('medecin.examens.show', $feuille)
            ->with('success', 'Feuille d\'examen enregistrée.');
    }

    public function show(FeuilleExamen $examen)
    {
        $this->authorizeView($examen);
        $examen->load(['patient', 'medecin.specialite', 'lignes', 'clinic']);
        $view = auth()->user()->isPatient() ? 'patient.examens.show' : 'medecin.examens.show';
        return view($view, ['feuille' => $examen]);
    }

    public function print(FeuilleExamen $examen)
    {
        $this->authorizeView($examen);
        $examen->load(['patient', 'medecin.specialite', 'lignes', 'clinic']);
        return view('medecin.examens.print', ['feuille' => $examen]);
    }

    private function authorizeView(FeuilleExamen $examen): void
    {
        $user = auth()->user();

        if ($user->isMedecin() && $user->medecin && $examen->medecin_id === $user->medecin->id) {
            return;
        }

        if ($user->isPatient() && $user->patient && $examen->patient_id === $user->patient->id) {
            return;
        }

        abort(403);
    }
}
