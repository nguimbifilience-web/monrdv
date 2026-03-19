<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use App\Models\Specialite;
use Illuminate\Http\Request;

class MedecinController extends Controller
{
    public function index(Request $request)
    {
        $query = Medecin::with('specialite');

        // TRI PAR CATÉGORIE (Spécialité)
        if ($request->filled('specialite_id')) {
            $query->where('specialite_id', $request->specialite_id);
        }

        // TRI ALPHABÉTIQUE
        $medecins = $query->orderBy('nom', 'asc')->get();
        $specialites = Specialite::orderBy('nom', 'asc')->get();

        return view('medecins.index', compact('medecins', 'specialites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'specialite_id' => 'required|exists:specialites,id',
        ]);

        Medecin::create($validated);
        return redirect()->route('medecins.index')->with('success', 'Médecin ajouté !');
    }
}