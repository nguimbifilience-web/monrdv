<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Specialite;

class MedecinController extends Controller
{
    public function index(Request $request)
    {
        // 1. Définir les paramètres de tri par défaut
        $sort = $request->get('sort', 'nom'); // Trie par 'nom' par défaut
        $direction = $request->get('direction', 'asc'); // Ordre croissant par défaut

        // 2. Sécurité : On vérifie que la colonne existe pour éviter les injections SQL
        $allowedSorts = ['nom', 'prenom', 'email'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'nom';
        }

        // 3. Récupérer les médecins avec le tri
        $medecins = Medecin::orderBy($sort, $direction)->get();

        // 4. Statistiques pour les compteurs en haut de page
        $stats = [
            'total_medecins' => Medecin::count(),
            'total_patients' => Patient::count(),
            'total_specialites' => Specialite::count(),
        ];

        // 5. Envoyer tout à la vue
        return view('medecins.index', compact('medecins', 'stats', 'sort', 'direction'));
    }
}