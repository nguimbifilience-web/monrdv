<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use App\Models\Specialite;
use Illuminate\Http\Request;

class MedecinController extends Controller
{
    // Affiche la liste simple (CRUD)
    public function index() 
    {
        $medecins = Medecin::with('specialite')->orderBy('id', 'desc')->get();
        return view('medecins.index', compact('medecins'));
    }

    public function create() 
    {
        $specialites = Specialite::all();
        return view('medecins.create', compact('specialites'));
    }

    public function store(Request $request) 
    {
        // On valide exactement ce que la base de données attend
        $validated = $request->validate([
            'nom'           => 'required',
            'prenom'        => 'required',
            'specialite_id' => 'required',
            'telephone'     => 'nullable',
        ]);

        // On crée le médecin avec les données validées
        Medecin::create($validated);

        return redirect()->route('medecins.index')->with('success', 'Médecin ajouté !');
    }

    // Affiche la page avec tous les calendriers FullCalendar
    public function schedule()
    {
        $medecins = Medecin::with(['specialite', 'disponibilites'])->get(); 
        return view('medecins.schedule', compact('medecins'));
    }

    public function showSchedule($id)
    {
        $medecin = Medecin::with(['specialite', 'disponibilites'])->findOrFail($id);
        return view('medecins.calendar', compact('medecin'));
    }

   // Affiche le formulaire de modification
    public function edit($id)
    {
        $medecin = Medecin::findOrFail($id);
        $specialites = Specialite::all(); // Nécessaire pour le menu déroulant des spécialités
        return view('medecins.edit', compact('medecin', 'specialites'));
    }

    // Enregistre les modifications
    public function update(Request $request, $id)
    {
        $medecin = Medecin::findOrFail($id);

        $validated = $request->validate([
            'nom'           => 'required',
            'prenom'        => 'required',
            'specialite_id' => 'required|exists:specialites,id',
            'telephone'     => 'nullable',
        ]);

        $medecin->update($validated);

        return redirect()->route('medecins.index')->with('success', 'Médecin mis à jour avec succès !');
    }

    // Supprime le médecin
    public function destroy($id)
    {
        $medecin = Medecin::findOrFail($id);
        $medecin->delete();

        return redirect()->route('medecins.index')->with('success', 'Le praticien a été retiré de la liste.');
    }

   public function toggleDispo(Request $request)
{
    // On récupère les données envoyées par ton JavaScript
    $medecinId = $request->input('medecin_id');
    $date = $request->input('date');

    // Ici, tu ajoutes ta logique pour enregistrer ou supprimer la dispo
    // Exemple rapide :
    $exists = \App\Models\Disponibilite::where('medecin_id', $medecinId)
                ->where('date_travail', $date)
                ->first();

    if ($exists) {
        $exists->delete();
        return response()->json(['status' => 'removed']);
    } else {
        \App\Models\Disponibilite::create([
            'medecin_id' => $medecinId,
            'date_travail' => $date
        ]);
        return response()->json(['status' => 'added']);
    }
}
}