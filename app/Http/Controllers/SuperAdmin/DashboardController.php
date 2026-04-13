<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\RendezVous;
use Illuminate\Http\Request;

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

        return view('superadmin.dashboard', compact('clinics', 'cities', 'view'));
    }
}
