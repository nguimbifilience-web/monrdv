<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\RendezVous;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Consultation;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Super admin → dashboard dédié avec stats par clinique
        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        }

        $nbRendezvous = RendezVous::whereDate('date_rv', today())->count();
        $nbMedecins = Medecin::count();
        $nbPatients = Patient::count();

        return view('dashboard', compact('nbRendezvous', 'nbMedecins', 'nbPatients'));
    }

    private function superAdminDashboard()
    {
        $clinics = Clinic::withCount(['users', 'patients', 'medecins', 'rendezvous', 'consultations'])
            ->orderBy('name')
            ->get();

        $stats = [
            'total_clinics' => $clinics->count(),
            'active_clinics' => $clinics->where('is_active', true)->count(),
            'total_users' => $clinics->sum('users_count'),
            'total_patients' => $clinics->sum('patients_count'),
            'total_medecins' => $clinics->sum('medecins_count'),
            'total_rdv_today' => RendezVous::withoutGlobalScopes()->whereDate('date_rv', today())->count(),
        ];

        return view('dashboard-superadmin', compact('clinics', 'stats'));
    }
}
