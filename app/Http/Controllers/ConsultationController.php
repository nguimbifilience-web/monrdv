<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\RendezVous;
use App\Http\Requests\StoreConsultationRequest;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Consultation::class);

        $query = Consultation::with(['patient.assurance', 'medecin.specialite']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%");
            });
        }

        if ($request->filled('medecin_id')) {
            $query->where('medecin_id', $request->medecin_id);
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $request->validate(['date_debut' => 'date', 'date_fin' => 'date']);
            $query->whereBetween('created_at', [$request->date_debut . ' 00:00:00', $request->date_fin . ' 23:59:59']);
        } elseif ($request->filled('date_debut')) {
            $request->validate(['date_debut' => 'date']);
            $query->whereDate('created_at', '>=', $request->date_debut);
        } elseif ($request->filled('date_fin')) {
            $request->validate(['date_fin' => 'date']);
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $consultations = $query->latest()->paginate(15)->withQueryString();

        $medecins = Medecin::orderBy('nom')->get();

        return view('consultations.index', compact('consultations', 'medecins'));
    }

    public function recettesMensuelles(Request $request)
    {
        $this->authorize('viewAny', Consultation::class);

        $mois = $request->input('mois', now()->month);
        $annee = $request->input('annee', now()->year);

        // Recettes par jour du mois sélectionné
        $recettesParJour = Consultation::selectRaw('DATE(created_at) as jour, COUNT(*) as nb_consultations, SUM(montant_patient) as recettes_patients, SUM(montant_assurance) as part_assurance, SUM(montant_total) as total_tarifs')
            ->whereMonth('created_at', $mois)
            ->whereYear('created_at', $annee)
            ->groupByRaw('DATE(created_at)')
            ->orderBy('jour')
            ->get();

        $totalMoisRecettes = $recettesParJour->sum('recettes_patients');
        $totalMoisAssurance = $recettesParJour->sum('part_assurance');
        $totalMoisConsultations = $recettesParJour->sum('nb_consultations');

        return view('consultations.recettes-mensuelles', compact(
            'recettesParJour', 'mois', 'annee',
            'totalMoisRecettes', 'totalMoisAssurance', 'totalMoisConsultations'
        ));
    }

    /**
     * Retourne les infos patient + son dernier RDV (médecin, spécialité, tarif) en JSON
     */
    public function getPatientInfo($id)
    {
        $patient = Patient::with('assurance')->findOrFail($id);

        // Chercher le dernier RDV non annulé du patient
        $dernierRdv = RendezVous::with('medecin.specialite')
            ->where('patient_id', $id)
            ->where('statut', '!=', 'annule')
            ->orderBy('date_rv', 'desc')
            ->orderBy('heure_rv', 'desc')
            ->first();

        return response()->json([
            'id'              => $patient->id,
            'nom'             => $patient->nom,
            'prenom'          => $patient->prenom,
            'telephone'       => $patient->telephone,
            'est_assure'      => $patient->est_assure,
            'assurance_nom'   => $patient->assurance->nom ?? null,
            'taux_couverture' => $patient->assurance->taux_couverture ?? 0,
            // Médecin du dernier RDV
            'medecin_id'      => $dernierRdv->medecin_id ?? null,
            'medecin_nom'     => $dernierRdv->medecin->nom ?? null,
            'medecin_prenom'  => $dernierRdv->medecin->prenom ?? null,
            'specialite_nom'  => $dernierRdv->medecin->specialite->nom ?? null,
            'tarif_consultation' => $dernierRdv->medecin->specialite->tarif_consultation ?? 0,
        ]);
    }

    /**
     * Enregistre une consultation
     */
    public function store(StoreConsultationRequest $request)
    {
        $this->authorize('create', Consultation::class);

        $validated = $request->validated();

        $patient = Patient::with('assurance')->findOrFail($validated['patient_id']);
        $medecin = Medecin::with('specialite')->findOrFail($validated['medecin_id']);

        // Le tarif complet de la spécialité
        $tarifSpecialite = $validated['tarif_specialite'] ?? $medecin->specialite->tarif_consultation ?? 0;
        $tauxCouverture = 0;

        if ($validated['est_assure'] && $patient->assurance) {
            $tauxCouverture = $patient->assurance->taux_couverture;
        }

        $montantAssurance = round($tarifSpecialite * $tauxCouverture / 100, 2);
        $montantPatient = round($tarifSpecialite - $montantAssurance, 2);
        $montantDonne = $validated['montant_donne'];
        $montantRendu = round($montantDonne - $montantPatient, 2);

        $consultation = Consultation::create([
            'patient_id'       => $validated['patient_id'],
            'medecin_id'       => $validated['medecin_id'],
            'montant_total'    => $tarifSpecialite,
            'taux_couverture'  => $tauxCouverture,
            'montant_assurance'=> $montantAssurance,
            'montant_patient'  => $montantPatient,
            'montant_donne'    => $montantDonne,
            'montant_rendu'    => max(0, $montantRendu),
        ]);

        ActivityLog::log(
            'creation',
            "Consultation #{$consultation->id} creee : patient #{$validated['patient_id']}, medecin #{$validated['medecin_id']}, montant patient {$montantPatient} F",
            $consultation
        );

        if ($request->input('action') === 'print') {
            return redirect()->route('consultations.ticket', $consultation->id);
        }

        return redirect()->route('rendezvous.index')->with('success', 'Consultation enregistrée.');
    }

    /**
     * Affiche le ticket imprimable
     */
    public function ticket($id)
    {
        $consultation = Consultation::with(['patient.assurance', 'medecin.specialite'])->findOrFail($id);
        $this->authorize('view', $consultation);

        return view('consultations.ticket', compact('consultation'));
    }
}
