<?php

namespace App\Http\Controllers;

use App\Models\Specialite;
use Illuminate\Http\Request;

class SpecialiteController extends Controller
{
    // Afficher la liste des spécialités
    public function index()
    {
        $specialites = Specialite::withCount('medecins')->get();
        return view('specialites.index', compact('specialites'));
    }

    // Formulaire de création
    public function create()
    {
        return view('specialites.create');
    }

    // Enregistrement dans la base
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|unique:specialites|max:255',
            'description' => 'nullable',
        ]);

        Specialite::create($request->all());

        return redirect()->route('specialites.index')
            ->with('success', 'Spécialité ajoutée avec succès !');
    }
}