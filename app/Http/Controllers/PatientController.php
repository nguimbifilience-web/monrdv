<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        // Tri par nom de A à Z
        $patients = Patient::orderBy('nom', 'asc')->get();
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'nullable|email',
            'telephone' => 'required',
        ]);

        Patient::create($validated);
        return redirect()->route('patients.index')->with('success', 'Patient ajouté !');
    }
}