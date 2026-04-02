<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use App\Models\RendezVous;
use App\Models\DocumentPatient;
use Illuminate\Http\Request;

class PatientPortalController extends Controller
{
    private function getPatient()
    {
        $patient = auth()->user()->patient;
        if (!$patient) {
            abort(403, 'Aucun profil patient associé à votre compte. Contactez l\'administrateur.');
        }
        return $patient;
    }

    public function dashboard()
    {
        $patient = $this->getPatient();

        $rdvsAVenir = RendezVous::with('medecin.specialite')
            ->where('patient_id', $patient->id)
            ->where('statut', 'confirme')
            ->where('date_rv', '>=', today())
            ->orderBy('date_rv')
            ->take(5)
            ->get();

        $totalRdv = RendezVous::where('patient_id', $patient->id)->count();
        $rdvEnAttente = RendezVous::where('patient_id', $patient->id)->where('statut', 'en_attente')->count();
        $rdvTermines = RendezVous::where('patient_id', $patient->id)->where('statut', 'termine')->count();

        return view('patient.dashboard', compact('patient', 'rdvsAVenir', 'totalRdv', 'rdvEnAttente', 'rdvTermines'));
    }

    public function mesRendezvous()
    {
        $patient = $this->getPatient();

        $rendezvous = RendezVous::with('medecin.specialite')
            ->where('patient_id', $patient->id)
            ->orderBy('date_rv', 'desc')
            ->orderBy('heure_rv', 'desc')
            ->paginate(10);

        return view('patient.rendezvous', compact('rendezvous'));
    }

    public function prendreRendezvous()
    {
        $medecins = Medecin::with('specialite')->orderBy('nom')->get();

        return view('patient.prendre-rdv', compact('medecins'));
    }

    /**
     * API : retourne le planning mensuel du médecin avec ses disponibilités
     */
    public function getDisponibilitesMedecin($medecinId)
    {
        $medecin = Medecin::findOrFail($medecinId);

        // Toutes les dates de travail à partir d'aujourd'hui
        $dispos = $medecin->disponibilites()
            ->where('date_travail', '>=', today())
            ->pluck('date_travail')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        // Compter les RDV par date pour vérifier la limite de 15
        $rdvParDate = RendezVous::where('medecin_id', $medecinId)
            ->where('date_rv', '>=', today())
            ->where('statut', '!=', 'annule')
            ->selectRaw('date_rv, COUNT(*) as total')
            ->groupBy('date_rv')
            ->pluck('total', 'date_rv')
            ->toArray();

        return response()->json([
            'medecin' => 'Dr. ' . $medecin->nom . ' ' . $medecin->prenom,
            'dispos' => $dispos,
            'rdv_par_date' => $rdvParDate,
        ]);
    }

    public function storeRendezvous(Request $request)
    {
        $patient = $this->getPatient();

        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'date_rv' => 'required|date|after_or_equal:today',
            'motif' => 'required|string|max:500',
        ]);

        // Vérifier la limite de 15 RDV par jour pour ce médecin
        $nbRdvJour = RendezVous::where('medecin_id', $request->medecin_id)
            ->where('date_rv', $request->date_rv)
            ->where('statut', '!=', 'annule')
            ->count();

        if ($nbRdvJour >= 15) {
            return back()->withErrors(['date_rv' => 'Ce médecin a atteint la limite de 15 rendez-vous pour cette journée.'])->withInput();
        }

        // Vérifier que le patient n'a pas déjà un RDV ce jour avec ce médecin
        $doublonPatient = RendezVous::where('patient_id', $patient->id)
            ->where('medecin_id', $request->medecin_id)
            ->where('date_rv', $request->date_rv)
            ->where('statut', '!=', 'annule')
            ->exists();

        if ($doublonPatient) {
            return back()->withErrors(['date_rv' => 'Vous avez déjà un rendez-vous avec ce médecin ce jour.'])->withInput();
        }

        RendezVous::create([
            'patient_id' => $patient->id,
            'medecin_id' => $request->medecin_id,
            'date_rv' => $request->date_rv,
            'motif' => $request->motif,
            'statut' => 'en_attente',
            'source' => 'en_ligne',
        ]);

        return redirect()->route('patient.rendezvous')->with('success', 'Demande de rendez-vous envoyée ! En attente de confirmation.');
    }

    public function annulerRendezvous($id)
    {
        $patient = $this->getPatient();

        $rdv = RendezVous::where('id', $id)
            ->where('patient_id', $patient->id)
            ->where('statut', 'en_attente')
            ->firstOrFail();

        $rdv->update(['statut' => 'annule']);

        return back()->with('success', 'Rendez-vous annulé.');
    }

    // ===== DOCUMENTS =====

    public function mesDocuments()
    {
        $patient = $this->getPatient();
        $documents = DocumentPatient::where('patient_id', $patient->id)->latest()->get();

        return view('patient.documents', compact('documents'));
    }

    public function uploadDocument(Request $request)
    {
        $patient = $this->getPatient();

        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:assurance,ordonnance,autre',
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('fichier')->store('documents/patients/' . $patient->id, 'public');

        DocumentPatient::create([
            'patient_id' => $patient->id,
            'nom' => $request->nom,
            'type' => $request->type,
            'fichier' => $path,
        ]);

        return back()->with('success', 'Document téléchargé avec succès.');
    }

    public function supprimerDocument($id)
    {
        $patient = $this->getPatient();

        $doc = DocumentPatient::where('id', $id)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        // Supprimer le fichier
        \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->fichier);

        $doc->delete();

        return back()->with('success', 'Document supprimé.');
    }
}
