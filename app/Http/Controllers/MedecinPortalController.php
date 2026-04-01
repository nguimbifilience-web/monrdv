<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedecinPortalController extends Controller
{
    private function getMedecin()
    {
        $medecin = auth()->user()->medecin;
        if (!$medecin) {
            abort(403, 'Aucune fiche médecin associée à votre compte. Contactez l\'administrateur.');
        }
        return $medecin;
    }

    public function dashboard()
    {
        $medecin = $this->getMedecin();

        $rdvsAujourdhui = RendezVous::with('patient')
            ->where('medecin_id', $medecin->id)
            ->whereDate('date_rv', today())
            ->where('statut', '!=', 'annule')
            ->orderBy('heure_rv')
            ->get();

        $totalRdv = RendezVous::where('medecin_id', $medecin->id)->count();
        $rdvAujourdhui = $rdvsAujourdhui->count();
        $totalPatients = RendezVous::where('medecin_id', $medecin->id)
            ->distinct('patient_id')->count('patient_id');

        return view('medecin.dashboard', compact('medecin', 'rdvsAujourdhui', 'totalRdv', 'rdvAujourdhui', 'totalPatients'));
    }

    public function planning()
    {
        $medecin = $this->getMedecin();
        $medecin->load('disponibilites', 'specialite');

        $dispos = $medecin->disponibilites()
            ->pluck('date_travail')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        // RDV par date pour afficher le nombre
        $rdvParDate = RendezVous::where('medecin_id', $medecin->id)
            ->where('statut', '!=', 'annule')
            ->selectRaw('date_rv, COUNT(*) as total')
            ->groupBy('date_rv')
            ->pluck('total', 'date_rv')
            ->toArray();

        return view('medecin.planning', compact('medecin', 'dispos', 'rdvParDate'));
    }

    public function mesRendezvous(Request $request)
    {
        $medecin = $this->getMedecin();

        $query = RendezVous::with('patient.assurance')
            ->where('medecin_id', $medecin->id);

        if ($request->filled('date')) {
            $query->whereDate('date_rv', $request->date);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $rendezvous = $query->orderBy('date_rv', 'desc')
            ->orderBy('heure_rv', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('medecin.rendezvous', compact('rendezvous'));
    }

    public function mesPatients()
    {
        $medecin = $this->getMedecin();

        $patients = Patient::whereHas('rendezvous', function ($q) use ($medecin) {
            $q->where('medecin_id', $medecin->id);
        })->with('assurance')->orderBy('nom')->paginate(15);

        return view('medecin.patients', compact('patients'));
    }
}
