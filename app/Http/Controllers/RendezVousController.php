<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\Disponibilite;
use App\Models\MotifConsultation;
use App\Models\ActivityLog;
use App\Http\Requests\StoreRendezVousRequest;
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
                $q->whereDate('date_rv', '<', $today)
                   ->orWhere(function ($q2) use ($today, $heure) {
                       $q2->whereDate('date_rv', $today)->where('heure_rv', '<', $heure);
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
        $this->authorize('viewAny', RendezVous::class);

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

        // En attente en premier, puis par date
        $rendezvous = $query->orderByRaw("CASE statut WHEN 'en_attente' THEN 1 WHEN 'confirme' THEN 2 WHEN 'termine' THEN 3 WHEN 'annule' THEN 4 ELSE 5 END")
            ->orderBy('date_rv', 'desc')
            ->paginate(15)
            ->withQueryString();
        $medecins = Medecin::with('specialite')->orderBy('nom')->get();

        return view('rendezvous.index', compact('rendezvous', 'medecins'));
    }

    public function create()
    {
        $this->authorize('create', RendezVous::class);

        $patients = Patient::orderBy('nom')->get();
        $medecins = Medecin::with('specialite')->orderBy('nom')->get();
        $patientsJson = $patients->map(function ($p) {
            return ['id' => $p->id, 'nom' => $p->nom, 'prenom' => $p->prenom, 'telephone' => $p->telephone];
        });
        return view('rendezvous.create', compact('patients', 'medecins', 'patientsJson'));
    }

    public function store(StoreRendezVousRequest $request)
    {
        $this->authorize('create', RendezVous::class);

        $validated = $request->validated();

        // Vérifier si le médecin travaille ce jour
        $travaille = Disponibilite::where('medecin_id', $validated['medecin_id'])
            ->whereDate('date_travail', $validated['date_rv'])
            ->exists();

        if (!$travaille) {
            return back()->withInput()->with('error', 'Ce médecin ne travaille pas ce jour-là. Vérifiez son planning.');
        }

        // Vérifier la limite de 20 RDV par jour
        $nbRdvJour = RendezVous::where('medecin_id', $validated['medecin_id'])
            ->whereDate('date_rv', $validated['date_rv'])
            ->where('statut', '!=', 'annule')
            ->count();

        if ($nbRdvJour >= 20) {
            return back()->withInput()->with('error', 'Ce médecin a atteint la limite de 20 rendez-vous pour cette journée.');
        }

        // RDV créé par le staff → statut confirmé directement
        $validated['statut'] = 'confirme';

        $rdv = RendezVous::create($validated);

        ActivityLog::log('creation', "RDV #{$rdv->id} cree : patient #{$validated['patient_id']}, medecin #{$validated['medecin_id']}, date {$validated['date_rv']}", $rdv);

        return redirect()->route('rendezvous.index')
                         ->with('success', 'Le rendez-vous a été enregistré !');
    }

    public function edit($id)
    {
        $rendezvous = RendezVous::findOrFail($id);
        $this->authorize('update', $rendezvous);
        $patients = Patient::orderBy('nom')->get();
        $medecins = Medecin::with('specialite')->orderBy('nom')->get();

        return view('rendezvous.edit', compact('rendezvous', 'patients', 'medecins'));
    }

    public function update(Request $request, $id)
    {
        $rendezvous = RendezVous::findOrFail($id);
        $this->authorize('update', $rendezvous);

        $validated = $request->validate([
            'date_rv'    => 'required|date',
            'heure_rv'   => 'required',
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'motif'      => 'nullable|string|max:255',
        ]);

        // Vérifier si le médecin travaille ce jour
        $travaille = Disponibilite::where('medecin_id', $validated['medecin_id'])
            ->whereDate('date_travail', $validated['date_rv'])
            ->exists();

        if (!$travaille) {
            return back()->withInput()->with('error', 'Ce médecin ne travaille pas ce jour-là.');
        }

        $doublePatient = RendezVous::where('patient_id', $validated['patient_id'])
            ->whereDate('date_rv', $validated['date_rv'])
            ->where('heure_rv', $validated['heure_rv'])
            ->where('statut', '!=', 'annule')
            ->where('id', '!=', $id)
            ->exists();

        if ($doublePatient) {
            return back()->withInput()->with('error', 'Ce patient a déjà un rendez-vous à cette date et heure.');
        }

        $medecinOccupe = RendezVous::where('medecin_id', $validated['medecin_id'])
            ->whereDate('date_rv', $validated['date_rv'])
            ->where('heure_rv', $validated['heure_rv'])
            ->where('statut', '!=', 'annule')
            ->where('id', '!=', $id)
            ->exists();

        if ($medecinOccupe) {
            return back()->withInput()->with('error', 'Ce médecin est déjà occupé à cette date et heure.');
        }

        $rendezvous->update($validated);

        ActivityLog::log('modification', "RDV #{$id} modifie", $rendezvous);

        return redirect()->route('rendezvous.index')
                         ->with('success', 'Rendez-vous mis à jour avec succès !');
    }

    public function destroy($id)
    {
        $rdv = RendezVous::findOrFail($id);
        $this->authorize('delete', $rdv);
        ActivityLog::log('suppression', "RDV supprimé : patient #{$rdv->patient_id}, médecin #{$rdv->medecin_id}, le {$rdv->date_rv}", $rdv);
        $rdv->delete();

        return redirect()->route('rendezvous.index')
                         ->with('success', 'Rendez-vous supprimé.');
    }

    public function annuler($id)
    {
        $rdv = RendezVous::findOrFail($id);
        $this->authorize('cancel', $rdv);
        $rdv->update(['statut' => 'annule']);

        ActivityLog::log('annulation', "RDV #{$id} annulé", $rdv);

        return back()->with('success', 'Rendez-vous annulé.');
    }

    public function confirmer($id)
    {
        $rdv = RendezVous::findOrFail($id);
        $this->authorize('update', $rdv);
        $rdv->update(['statut' => 'confirme']);

        ActivityLog::log('modification', "RDV #{$id} confirme", $rdv);

        return back()->with('success', 'Rendez-vous confirmé !');
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
            ->whereDate('date_rv', $request->date)
            ->where('statut', '!=', 'annule')
            ->pluck('heure_rv')
            ->map(fn($h) => substr($h, 0, 5))
            ->toArray();

        $travaille = Disponibilite::where('medecin_id', $request->medecin_id)
            ->whereDate('date_travail', $request->date)
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

    /**
     * Motifs de consultation pour un médecin (via sa spécialité) — AJAX
     */
    public function motifsByMedecin(Request $request)
    {
        $request->validate(['medecin_id' => 'required|exists:medecins,id']);

        $medecin = Medecin::findOrFail($request->medecin_id);

        $motifs = $medecin->specialite_id
            ? MotifConsultation::where('specialite_id', $medecin->specialite_id)
                ->orderBy('libelle')
                ->pluck('libelle')
            : collect();

        return response()->json(['motifs' => $motifs]);
    }
}
