<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use Illuminate\Http\Request;

class RendezVousController extends Controller
{
    // Afficher le formulaire de création
    public function create()
    {
        $patients = Patient::all();
        $medecins = Medecin::all();
        return view('rendez_vous.create', compact('patients', 'medecins'));
    }

    // Enregistrer le rendez-vous dans la base
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'date_rdv' => 'required|date|after:now',
            'motif' => 'nullable|string',
        ]);

        RendezVous::create($request->all());

        return redirect('/medecins')->with('success', 'Rendez-vous enregistré !');
    }
}