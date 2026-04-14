<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function patients(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Patient::class);

        $patients = Patient::with(['medecin', 'assurance'])->get();

        return $this->streamCsv('patients.csv',
            ['Nom', 'Prénom', 'Téléphone', 'Email', 'Quartier', 'Assuré', 'Assurance', 'Médecin traitant', 'Date inscription'],
            $patients->map(fn($p) => [
                $p->nom,
                $p->prenom,
                $p->telephone,
                $p->email ?? '',
                $p->quartier ?? '',
                $p->est_assure ? 'Oui' : 'Non',
                $p->assurance->nom ?? '',
                $p->medecin ? 'Dr. ' . $p->medecin->nom : '',
                $p->created_at->format('d/m/Y'),
            ])->toArray()
        );
    }

    public function rendezvous(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', RendezVous::class);

        $query = RendezVous::with(['patient', 'medecin.specialite']);

        if ($request->filled('date_debut')) {
            $query->where('date_rv', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('date_rv', '<=', $request->date_fin);
        }

        $rdvs = $query->orderBy('date_rv', 'desc')->get();

        return $this->streamCsv('rendezvous.csv',
            ['Date', 'Heure', 'Patient', 'Médecin', 'Spécialité', 'Motif', 'Statut', 'Source'],
            $rdvs->map(fn($r) => [
                \Carbon\Carbon::parse($r->date_rv)->format('d/m/Y'),
                $r->heure_rv ? substr($r->heure_rv, 0, 5) : '',
                ($r->patient->nom ?? '') . ' ' . ($r->patient->prenom ?? ''),
                $r->medecin ? 'Dr. ' . $r->medecin->nom : '',
                $r->medecin?->specialite?->nom ?? '',
                $r->motif ?? '',
                $r->statut,
                $r->source ?? 'staff',
            ])->toArray()
        );
    }

    public function consultations(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Consultation::class);

        $query = Consultation::with(['patient.assurance', 'medecin.specialite']);

        if ($request->filled('mois') && $request->filled('annee')) {
            $query->whereMonth('created_at', $request->mois)
                  ->whereYear('created_at', $request->annee);
        }

        $consultations = $query->orderBy('created_at', 'desc')->get();

        return $this->streamCsv('consultations.csv',
            ['Date', 'Patient', 'Médecin', 'Spécialité', 'Montant total', 'Taux couverture', 'Part assurance', 'Part patient', 'Montant donné', 'Montant rendu'],
            $consultations->map(fn($c) => [
                $c->created_at->format('d/m/Y H:i'),
                ($c->patient->nom ?? '') . ' ' . ($c->patient->prenom ?? ''),
                $c->medecin ? 'Dr. ' . $c->medecin->nom : '',
                $c->medecin?->specialite?->nom ?? '',
                number_format($c->montant_total, 0, ',', ' '),
                $c->taux_couverture . '%',
                number_format($c->montant_assurance, 0, ',', ' '),
                number_format($c->montant_patient, 0, ',', ' '),
                number_format($c->montant_donne, 0, ',', ' '),
                number_format($c->montant_rendu, 0, ',', ' '),
            ])->toArray()
        );
    }

    public function recettes(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Consultation::class);

        $mois = $request->input('mois', now()->month);
        $annee = $request->input('annee', now()->year);

        $recettes = Consultation::selectRaw('DATE(created_at) as jour, COUNT(*) as nb, SUM(montant_patient) as recettes, SUM(montant_assurance) as assurance, SUM(montant_total) as total')
            ->whereMonth('created_at', $mois)
            ->whereYear('created_at', $annee)
            ->groupByRaw('DATE(created_at)')
            ->orderBy('jour')
            ->get();

        return $this->streamCsv("recettes_{$mois}_{$annee}.csv",
            ['Jour', 'Nb consultations', 'Recettes patients', 'Part assurance', 'Total tarifs'],
            $recettes->map(fn($r) => [
                \Carbon\Carbon::parse($r->jour)->format('d/m/Y'),
                $r->nb,
                number_format($r->recettes, 0, ',', ' '),
                number_format($r->assurance, 0, ',', ' '),
                number_format($r->total, 0, ',', ' '),
            ])->toArray()
        );
    }

    private function streamCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers, ';');
            foreach ($rows as $row) {
                fputcsv($handle, $row, ';');
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
