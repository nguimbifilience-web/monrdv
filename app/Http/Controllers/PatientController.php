<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Medecin;
use App\Models\Assurance;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Affiche la liste des patients
     */
    public function index(Request $request)
    {
        $patients = Patient::with('medecin')
            ->filter($request->only(['search', 'medecin_id', 'est_assure']))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $medecins = Medecin::all();

        return view('patients.index', compact('patients', 'medecins'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create() 
    {
        $medecins = Medecin::all();
        $assurances = Assurance::all();

        return view('patients.create', compact('medecins', 'assurances'));
    }

    /**
     * Enregistre un nouveau patient
     */
    public function store(Request $request) 
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required',
            'est_assure' => 'required|boolean',
            'medecin_id' => 'nullable|exists:medecins,id',
        ]);

        Patient::create($request->all());

        return redirect()->route('patients.index')
                         ->with('success', 'Le patient a été enregistré avec succès.');
    }

    /**
     * Affiche les détails d'un patient
     */
    public function show(Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Patient $patient)
    {
        $medecins = Medecin::all();
        $assurances = Assurance::all();

        return view('patients.edit', compact('patient', 'medecins', 'assurances'));
    }

    /**
     * Met à jour un patient
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required',
        ]);

        $patient->update($request->all());

        return redirect()->route('patients.index')
                         ->with('success', 'Dossier patient mis à jour.');
    }

    /**
     * Supprime un patient
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')
                         ->with('success', 'Patient supprimé.');
    }
}