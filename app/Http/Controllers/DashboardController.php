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
