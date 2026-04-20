<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\RendezVous;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\User;
use App\Models\Consultation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Super admin → dashboard dédié (vue par clinique)
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        $nbRendezvous = RendezVous::whereDate('date_rv', today())->count();
        $nbMedecins = Medecin::count();
        $nbPatients = Patient::count();

        // Comptes pour la section gestion
        if ($user->isAdmin()) {
            // Admin voit tous les comptes de sa clinique
            // CASE au lieu de FIELD() pour rester portable MySQL / SQLite
            $comptes = User::where('role', '!=', 'super_admin')
                ->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'secretaire' THEN 2 WHEN 'medecin' THEN 3 WHEN 'patient' THEN 4 ELSE 5 END")
                ->orderBy('name')->get();
        } else {
            // Secrétaire voit seulement les patients
            $comptes = User::where('role', 'patient')->orderBy('name')->get();
        }

        $stats = $this->computeStats();

        return view('dashboard', compact('nbRendezvous', 'nbMedecins', 'nbPatients', 'comptes', 'stats'));
    }

    private function computeStats(): array
    {
        $debutMois = Carbon::now()->startOfMonth();
        $debutMoisPrec = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $finMoisPrec = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        // Recettes mois courant vs precedent (part patient encaissee)
        $recettesMois = (float) Consultation::where('created_at', '>=', $debutMois)
            ->sum('montant_patient');
        $recettesMoisPrec = (float) Consultation::whereBetween('created_at', [$debutMoisPrec, $finMoisPrec])
            ->sum('montant_patient');
        $evolution = $recettesMoisPrec > 0
            ? round((($recettesMois - $recettesMoisPrec) / $recettesMoisPrec) * 100, 1)
            : null;

        // Repartition RDV par statut sur les 30 derniers jours
        $rdvParStatut = RendezVous::select('statut', DB::raw('COUNT(*) as total'))
            ->where('date_rv', '>=', Carbon::now()->subDays(30)->toDateString())
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();

        // Taux d'annulation sur 30 jours
        $totalRdv30j = array_sum($rdvParStatut);
        $tauxAnnulation = $totalRdv30j > 0
            ? round((($rdvParStatut['annule'] ?? 0) / $totalRdv30j) * 100, 1)
            : 0;

        // Consultations par jour sur 7 derniers jours (1 query au lieu de 7)
        $debut7j = Carbon::now()->subDays(6)->startOfDay();
        $consultationsParJour = Consultation::select(DB::raw('DATE(created_at) as jour'), DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', $debut7j)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'jour')
            ->toArray();

        $consultations7j = [];
        for ($i = 6; $i >= 0; $i--) {
            $jour = Carbon::now()->subDays($i);
            $cle = $jour->toDateString();
            $consultations7j[] = [
                'label' => $jour->isoFormat('ddd D'),
                'total' => (int) ($consultationsParJour[$cle] ?? 0),
            ];
        }

        // Top 5 medecins sur le mois (par nombre de consultations)
        $topMedecins = Consultation::select('medecin_id', DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', $debutMois)
            ->groupBy('medecin_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('medecin:id,nom,prenom')
            ->get()
            ->map(fn ($row) => [
                'nom' => $row->medecin ? "Dr. {$row->medecin->nom} {$row->medecin->prenom}" : 'Inconnu',
                'total' => (int) $row->total,
            ])
            ->toArray();

        return compact(
            'recettesMois', 'recettesMoisPrec', 'evolution',
            'rdvParStatut', 'tauxAnnulation', 'totalRdv30j',
            'consultations7j', 'topMedecins'
        );
    }

    public function logs()
    {
        $query = \App\Models\ActivityLog::with('user')->latest();

        if (request('user_id')) {
            $query->where('user_id', request('user_id'));
        }
        if (request('action')) {
            $query->where('action', request('action'));
        }
        if (request('date')) {
            $query->whereDate('created_at', request('date'));
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = \App\Models\User::where('clinic_id', auth()->user()->clinic_id)->orderBy('name')->get();

        return view('activites.index', compact('logs', 'users'));
    }

}
