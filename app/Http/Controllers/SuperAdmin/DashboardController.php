<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\RendezVous;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Vue par clinique (cartes + tableau togglables).
     * AUCUNE stat globale agrégée.
     */
    public function index(Request $request)
    {
        $query = Clinic::with('plan')
            ->withCount(['medecins', 'patients']);

        if ($search = $request->get('q')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($status = $request->get('status')) {
            match ($status) {
                'active' => $query->where('is_active', true)->where('is_blocked', false),
                'suspended' => $query->where(fn ($q) => $q->where('is_active', false)->orWhere('is_blocked', true)),
                default => null,
            };
        }
        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }

        $clinics = $query->orderBy('name')->get();

        // Stats mensuelles PAR clinique (RDV du mois courant + revenus du mois)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $rdvPerClinic = RendezVous::withoutGlobalScopes()
            ->selectRaw('clinic_id, COUNT(*) as total')
            ->whereBetween('date_rv', [$startOfMonth, $endOfMonth])
            ->groupBy('clinic_id')
            ->pluck('total', 'clinic_id');

        $revenuePerClinic = Consultation::withoutGlobalScopes()
            ->selectRaw('clinic_id, SUM(montant_patient) as total')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('clinic_id')
            ->pluck('total', 'clinic_id');

        foreach ($clinics as $clinic) {
            $clinic->rdv_this_month = $rdvPerClinic[$clinic->id] ?? 0;
            $clinic->revenue_this_month = $revenuePerClinic[$clinic->id] ?? 0;
        }

        $cities = Clinic::whereNotNull('city')->distinct()->pluck('city');
        $view = $request->get('view', 'cards'); // cards | table

        $globalStats = $this->computeGlobalStats();

        return view('superadmin.dashboard', compact('clinics', 'cities', 'view', 'globalStats'));
    }

    private function computeGlobalStats(): array
    {
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();
        $debutMoisPrec = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $finMoisPrec = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        // KPI cliniques
        $totalClinics = Clinic::count();
        $activeClinics = Clinic::where('is_active', true)->where('is_blocked', false)->count();
        $blockedClinics = Clinic::where('is_blocked', true)->count();
        $inactiveClinics = Clinic::where('is_active', false)->count();

        // Recettes cumulees mois courant vs precedent
        $revenueMois = (float) Consultation::withoutGlobalScopes()
            ->whereBetween('created_at', [$debutMois, $finMois])
            ->sum('montant_patient');
        $revenueMoisPrec = (float) Consultation::withoutGlobalScopes()
            ->whereBetween('created_at', [$debutMoisPrec, $finMoisPrec])
            ->sum('montant_patient');
        $revenueEvolution = $revenueMoisPrec > 0
            ? round((($revenueMois - $revenueMoisPrec) / $revenueMoisPrec) * 100, 1)
            : null;

        // RDV cumules mois courant
        $rdvMois = RendezVous::withoutGlobalScopes()
            ->whereBetween('date_rv', [$debutMois->toDateString(), $finMois->toDateString()])
            ->count();

        // Utilisateurs totaux (hors super_admin)
        $totalUsers = User::withoutGlobalScopes()->where('role', '!=', 'super_admin')->count();

        // Top 5 cliniques par recettes du mois
        $topRevenue = Consultation::withoutGlobalScopes()
            ->select('clinic_id', DB::raw('SUM(montant_patient) as total'))
            ->whereBetween('created_at', [$debutMois, $finMois])
            ->groupBy('clinic_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('clinic:id,name')
            ->get()
            ->map(fn ($row) => [
                'nom' => $row->clinic?->name ?? 'Inconnue',
                'total' => (float) $row->total,
            ])
            ->toArray();

        // Top 5 cliniques par RDV du mois
        $topRdv = RendezVous::withoutGlobalScopes()
            ->select('clinic_id', DB::raw('COUNT(*) as total'))
            ->whereBetween('date_rv', [$debutMois->toDateString(), $finMois->toDateString()])
            ->groupBy('clinic_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('clinic:id,name')
            ->get()
            ->map(fn ($row) => [
                'nom' => $row->clinic?->name ?? 'Inconnue',
                'total' => (int) $row->total,
            ])
            ->toArray();

        // Nouvelles cliniques par mois sur 6 mois
        $croissance = [];
        for ($i = 5; $i >= 0; $i--) {
            $debut = Carbon::now()->subMonthsNoOverflow($i)->startOfMonth();
            $fin = Carbon::now()->subMonthsNoOverflow($i)->endOfMonth();
            $croissance[] = [
                'label' => $debut->isoFormat('MMM YY'),
                'total' => Clinic::whereBetween('created_at', [$debut, $fin])->count(),
            ];
        }

        return compact(
            'totalClinics', 'activeClinics', 'blockedClinics', 'inactiveClinics',
            'revenueMois', 'revenueMoisPrec', 'revenueEvolution',
            'rdvMois', 'totalUsers',
            'topRevenue', 'topRdv', 'croissance'
        );
    }
}
