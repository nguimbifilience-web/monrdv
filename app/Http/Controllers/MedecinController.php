<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use App\Models\Specialite;
use App\Models\User;
use App\Models\ActivityLog;
use App\Http\Requests\StoreMedecinRequest;
use App\Http\Requests\UpdateMedecinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MedecinController extends Controller
{
    public function index(Request $request)
    {
        $query = Medecin::with(['specialite', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('specialite_id')) {
            $query->where('specialite_id', $request->specialite_id);
        }

        $medecins = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $specialites = Specialite::orderBy('nom')->get();

        return view('medecins.index', compact('medecins', 'specialites'));
    }

    public function create() 
    {
        $specialites = Specialite::all();
        return view('medecins.create', compact('specialites'));
    }

    public function store(StoreMedecinRequest $request)
    {
        $validated = $request->validated();

        // Générer un mot de passe
        $password = Str::password(12);

        // Créer le compte utilisateur du médecin
        $user = User::create([
            'name' => 'Dr. ' . $validated['nom'] . ' ' . $validated['prenom'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => 'medecin',
            'clinic_id' => auth()->user()->clinic_id,
            'email_verified_at' => now(),
        ]);

        // Créer le médecin lié au compte
        $medecinData = collect($validated)->except('email')->toArray();
        $medecinData['user_id'] = $user->id;
        $medecin = Medecin::create($medecinData);

        ActivityLog::log('creation', "Medecin #{$medecin->id} cree", $medecin);

        return redirect()->route('medecins.index')
            ->with('success', "Médecin ajouté ! Identifiants : {$validated['email']} / {$password}");
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
    public function update(UpdateMedecinRequest $request, $id)
    {
        $medecin = Medecin::findOrFail($id);

        $validated = $request->validated();

        $medecin->update($validated);
        ActivityLog::log('modification', "Medecin #{$medecin->id} modifie", $medecin);

        return redirect()->route('medecins.index')->with('success', 'Médecin mis à jour avec succès !');
    }

    // Supprime le médecin
    public function destroy($id)
    {
        $medecin = Medecin::findOrFail($id);
        ActivityLog::log('suppression', "Medecin #{$medecin->id} supprime", $medecin);
        $medecin->delete();

        return redirect()->route('medecins.index')->with('success', 'Le praticien a été retiré de la liste.');
    }

    /**
     * Recherche AJAX médecins
     */
    public function ajaxSearch(Request $request)
    {
        $query = Medecin::with('specialite');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('specialite_id')) {
            $query->where('specialite_id', $request->specialite_id);
        }

        $medecins = $query->orderBy('id', 'desc')->limit(50)->get();

        return response()->json($medecins->map(function ($m) {
            return [
                'id' => $m->id,
                'nom' => $m->nom,
                'prenom' => $m->prenom,
                'telephone' => $m->telephone,
                'email' => $m->email,
                'specialite' => $m->specialite->nom ?? 'Généraliste',
                'tarif_heure' => $m->tarif_heure,
                'heures_mois' => $m->heures_mois,
                'montant_total' => $m->tarif_heure * $m->heures_mois,
                'edit_url' => route('medecins.edit', $m->id),
                'delete_url' => route('medecins.destroy', $m->id),
            ];
        }));
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