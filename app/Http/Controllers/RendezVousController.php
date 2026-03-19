<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RendezVous;
use App\Models\Medecin;
use App\Models\Patient;
use Carbon\Carbon;

class RendezVousController extends Controller
{
    public function index(Request $request)
    {
        // 1. Paramètres des filtres
        $medecinId = $request->get('medecin_id');
        $dateFiltre = $request->get('date', Carbon::today()->format('Y-m-d'));
        $direction = $request->get('direction', 'asc');

        // 2. LISTE A : Le Planning du jour (Filtrable par date/médecin)
        $queryPlanning = RendezVous::with(['medecin', 'patient'])
            ->whereDate('date_rdv', $dateFiltre);

        if ($medecinId) {
            $queryPlanning->where('medecin_id', $medecinId);
        }
        $rendezvous = $queryPlanning->orderBy('date_rdv', $direction)->get();

        // 3. LISTE B : Flux d'activité (Les 20 derniers enregistrements réels)
        $derniersRdv = RendezVous::with(['medecin', 'patient'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        // 4. Données pour la vue
        $medecins = Medecin::all();
        $totalRdvJour = $rendezvous->count();

        return view('rendezvous.index', compact(
            'rendezvous', 
            'derniersRdv', 
            'medecins', 
            'medecinId', 
            'dateFiltre', 
            'direction', 
            'totalRdvJour'
        ));
    }

    public function create()
    {
        $medecins = Medecin::with('specialite')->get();
        $patients = Patient::orderBy('nom', 'asc')->get();
        return view('rendezvous.create', compact('medecins', 'patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'patient_id' => 'required|exists:patients,id',
            'date_rdv'   => 'required|date',
        ]);

        RendezVous::create($request->all());

        // Redirection vers l'index pour voir le nouveau RDV apparaître dans le flux
        return redirect()->route('rendezvous.index')->with('success', 'Rendez-vous enregistré avec succès !');
    }
}