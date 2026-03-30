<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\Disponibilite;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RendezVousController extends Controller
{
    /**
     * Met à jour automatiquement les RDV dont l'heure est dépassée
     */
    private function updateStatutsDepasses()
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $heure = $now->format('H:i:s');

        $rdvDepasses = RendezVous::where('statut', 'en_attente')
            ->where(function ($q) use ($today, $heure) {
                $q->where('date_rv', '<', $today)
                   ->orWhere(function ($q2) use ($today, $heure) {
                       $q2->where('date_rv', $today)->where('heure_rv', '<', $heure);
                   });
            })->get();

        foreach ($rdvDepasses as $rdv) {
            if ($rdv->consultation()->exists()) {
                $rdv->update(['statut' => 'termine']);
            } else {
                $rdv->update(['statut' => 'annule']);
            }
        }
    }

    public function index(Request $request)
    {
        $this->updateStatutsDepasses();

        $query = RendezVous::with(['patient.assurance', 'medecin.specialite', 'consultation']);

        if ($request->filled('search')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date_rv', $request->date);
        }

        if ($request->filled('medecin_id')) {
            $query->where('medecin_id', $request->medecin_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $rendezvous = $query->orderBy('date_rv', 'desc')->orderBy('heure_rv', 'asc')->paginate(15)->withQueryString();
        $medecins = Medecin::with('specialite')->orderBy('nom')->get();

        return view('rendezvous.index', compact('rendezvous', 'medecins'));
    }

    public function create()
    {
        $patients = Patient::orderBy('nom')->get();
        $medecins = Medecin::with('specialite')->orderBy('nom')->get();
        $patientsJson = $patients->map(function ($p) {
            return ['id' => $p->id, 'nom' => $p->nom, 'prenom' => $p->prenom, 'telephone' => $p->telephone];
        });
        return view('rendezvous.create', compact('patients', 'medecins', 'patientsJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_rv'    => 'required|date',
            'heure_rv'   => 'required',
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'motif'      => 'nullable|string|max:255',
        ]);

        // Vérifier si le médecin travaille ce jour
        $travaille = Disponibilite::where('medecin_id', $validated['medecin_id'])
            ->where('date_travail', $validated['date_rv'])
            ->exists();

        if (!$travaille) {
            return back()->withInput()->with('error', 'Ce médecin ne travaille pas ce jour-là. Vérifiez son planning.');
        }

        // Vérifier double réservation patient
        $doublePatient = RendezVous::where('patient_id', $validated['patient_id'])
            ->where('date_rv', $validated['date_rv'])
            ->where('heure_rv', $validated['heure_rv'])
            ->where('statut', '!=', 'annule')
            ->exists();

        if ($doublePatient) {
            return back()->withInput()->with('error', 'Ce patient a déjà un rendez-vous à cette date et heure.');
        }

        // Vérifier médecin déjà occupé
        $medecinOccupe = RendezVous::where('medecin_id', $validated['medecin_id'])
            ->where('date_rv', $validated['date_rv'])
            ->where('heure_rv', $validated['heure_rv'])
            ->where('statut', '!=', 'annule')
            ->exists();

        if ($medecinOccupe) {
            return back()->withInput()->with('error', 'Ce médecin est déjà occupé à cette date et heure.');
        }

        $rdv = RendezVous::create($validated);

        $patient = Patient::find($validated['patient_id']);
        $medecin = Medecin::find($validated['medecin_id']);
        ActivityLog::log('creation', "RDV créé : {$patient->nom} {$patient->prenom} avec Dr. {$medecin->nom} le {$validated['date_rv']} à {$validated['heure_rv']}", $rdv);

        return redirect()->route('rendezvous.index')
                         ->with('success', 'Le rendez-vous a été enregistré !');
    }

    public function edit($id)
    {
        $rendezvous = RendezVous::findOrFail($id);
        $patients = Patient::orderBy('nom')->get();
        $medecins = Medecin::with('specialite')->orderBy('nom')->get();

        return view('rendezvous.edit', compact('rendezvous', 'patients', 'medecins'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'date_rv'    => 'required|date',
            'heure_rv'   => 'required',
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'motif'      => 'nullable|string|max:255',
        ]);

        // Vérifier si le médecin travaille ce jour
        $travaille = Disponibilite::where('medecin_id', $validated['medecin_id'])
            ->where('date_travail', $validated['date_rv'])
            ->exists();

        if (!$travaille) {
            return back()->withInput()->with('error', 'Ce médecin ne travaille pas ce jour-là.');
        }

        $doublePatient = RendezVous::where('patient_id', $validated['patient_id'])
            ->where('date_rv', $validated['date_rv'])
            ->where('heure_rv', $validated['heure_rv'])
            ->where('statut', '!=', 'annule')
            ->where('id', '!=', $id)
            ->exists();

        if ($doublePatient) {
            return back()->withInput()->with('error', 'Ce patient a déjà un rendez-vous à cette date et heure.');
        }

        $medecinOccupe = RendezVous::where('medecin_id', $validated['medecin_id'])
            ->where('date_rv', $validated['date_rv'])
            ->where('heure_rv', $validated['heure_rv'])
            ->where('statut', '!=', 'annule')
            ->where('id', '!=', $id)
            ->exists();

        if ($medecinOccupe) {
            return back()->withInput()->with('error', 'Ce médecin est déjà occupé à cette date et heure.');
        }

        $rendezvous = RendezVous::findOrFail($id);
        $oldValues = $rendezvous->toArray();
        $rendezvous->update($validated);

        ActivityLog::log('modification', "RDV #{$id} modifié", $rendezvous, $oldValues, $validated);

        return redirect()->route('rendezvous.index')
                         ->with('success', 'Rendez-vous mis à jour avec succès !');
    }

    public function destroy($id)
    {
        $rdv = RendezVous::findOrFail($id);
        ActivityLog::log('suppression', "RDV supprimé : patient #{$rdv->patient_id}, médecin #{$rdv->medecin_id}, le {$rdv->date_rv}", $rdv);
        $rdv->delete();

        return redirect()->route('rendezvous.index')
                         ->with('success', 'Rendez-vous supprimé.');
    }

    public function annuler($id)
    {
        $rdv = RendezVous::findOrFail($id);
        $rdv->update(['statut' => 'annule']);

        ActivityLog::log('annulation', "RDV #{$id} annulé", $rdv);

        return back()->with('success', 'Rendez-vous annulé.');
    }

    /**
     * Créneaux disponibles pour un médecin à une date (AJAX)
     */
    public function creneauxDisponibles(Request $request)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'date' => 'required|date',
        ]);

        $tousCreneaux = [];
        for ($h = 8; $h < 18; $h++) {
            $tousCreneaux[] = sprintf('%02d:00', $h);
            $tousCreneaux[] = sprintf('%02d:30', $h);
        }

        $pris = RendezVous::where('medecin_id', $request->medecin_id)
            ->where('date_rv', $request->date)
            ->where('statut', '!=', 'annule')
            ->pluck('heure_rv')
            ->map(fn($h) => substr($h, 0, 5))
            ->toArray();

        $travaille = Disponibilite::where('medecin_id', $request->medecin_id)
            ->where('date_travail', $request->date)
            ->exists();

        $creneaux = [];
        foreach ($tousCreneaux as $c) {
            $creneaux[] = [
                'heure' => $c,
                'disponible' => $travaille && !in_array($c, $pris),
            ];
        }

        return response()->json([
            'travaille' => $travaille,
            'creneaux' => $creneaux,
        ]);
    }
}
