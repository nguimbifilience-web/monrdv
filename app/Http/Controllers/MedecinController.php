<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medecin; // Vérifie que ton modèle existe
use App\Models\Patient;
use App\Models\Specialite;

class MedecinController extends Controller
{
    public function index()
    {
        // On prépare les statistiques pour la vue
        $stats = [
            'total_medecins' => Medecin::count(),
            'total_patients' => Patient::count(),
            'total_specialites' => Specialite::count(),
        ];

        // On récupère la liste des médecins
        $medecins = Medecin::all();

        // IMPORTANT : 'medecins.index' veut dire dossier "medecins" -> fichier "index.blade.php"
        return view('medecins.index', compact('stats', 'medecins'));
    }
}