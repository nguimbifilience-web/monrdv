<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\MotifConsultation;
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

        $motifs = $medecin->specialite_id
            ? MotifConsultation::where('specialite_id', $medecin->specialite_id)->orderBy('libelle')->pluck('libelle')
            : collect();

        return view('medecin.dashboard', compact('medecin', 'rdvsAujourdhui', 'totalRdv', 'rdvAujourdhui', 'totalPatients', 'motifs'));
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

        $motifs = $medecin->specialite_id
            ? MotifConsultation::where('specialite_id', $medecin->specialite_id)->orderBy('libelle')->pluck('libelle')
            : collect();

        return view('medecin.rendezvous', compact('rendezvous', 'motifs'));
    }

    /**
     * Programmer le prochain RDV pour un patient (depuis la vue médecin)
     */
    public function programmerProchainRdv(Request $request)
    {
        $medecin = $this->getMedecin();

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'motif' => 'required|string|max:500',
            'date_rv' => 'required|date|after:today',
        ]);

        $rdv = RendezVous::create([
            'clinic_id' => $medecin->clinic_id,
            'patient_id' => $request->patient_id,
            'medecin_id' => $medecin->id,
            'date_rv' => $request->date_rv,
            'motif' => $request->motif,
            'statut' => 'en_attente',
            'source' => 'staff',
        ]);

        return back()->with('success', 'Prochain rendez-vous programmé pour le ' . \Carbon\Carbon::parse($request->date_rv)->format('d/m/Y') . '.');
    }

    public function mesPatients()
    {
        $medecin = $this->getMedecin();

        $patients = Patient::whereHas('rendezvous', function ($q) use ($medecin) {
            $q->where('medecin_id', $medecin->id);
        })->with('assurance')->orderBy('nom')->paginate(15);

        $motifs = $medecin->specialite_id
            ? MotifConsultation::where('specialite_id', $medecin->specialite_id)->orderBy('libelle')->pluck('libelle')
            : collect();

        return view('medecin.patients', compact('patients', 'motifs'));
    }

    /**
     * Dossier d'un patient (vue médecin)
     */
    public function dossierPatient($id)
    {
        $medecin = $this->getMedecin();

        $patient = Patient::with('assurance')->findOrFail($id);

        // Vérifier que ce médecin a bien vu ce patient
        $aAcces = RendezVous::where('medecin_id', $medecin->id)
            ->where('patient_id', $patient->id)
            ->exists();

        if (!$aAcces) {
            abort(403, 'Vous n\'avez pas accès au dossier de ce patient.');
        }

        $rendezvous = RendezVous::where('medecin_id', $medecin->id)
            ->where('patient_id', $patient->id)
            ->orderBy('date_rv', 'desc')
            ->get();

        $motifs = $medecin->specialite_id
            ? MotifConsultation::where('specialite_id', $medecin->specialite_id)->orderBy('libelle')->pluck('libelle')
            : collect();

        return view('medecin.dossier-patient', compact('patient', 'rendezvous', 'motifs', 'medecin'));
    }

    /**
     * Sauvegarder les notes médicales d'un patient
     */
    public function sauvegarderNotes(Request $request, $id)
    {
        $medecin = $this->getMedecin();

        $patient = Patient::findOrFail($id);

        // Vérifier l'accès
        $aAcces = RendezVous::where('medecin_id', $medecin->id)
            ->where('patient_id', $patient->id)
            ->exists();

        if (!$aAcces) {
            abort(403);
        }

        $request->validate([
            'notes_medicales' => 'nullable|string|max:5000',
            'observations' => 'nullable|string|max:5000',
        ]);

        $patient->update([
            'notes_medicales' => $request->notes_medicales,
            'observations' => $request->observations,
        ]);

        return back()->with('success', 'Notes sauvegardées.');
    }
}
