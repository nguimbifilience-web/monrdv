<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\RendezVous;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\User;
use App\Models\Consultation;

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
            $comptes = User::where('role', '!=', 'super_admin')
                ->orderByRaw("FIELD(role, 'admin', 'secretaire', 'medecin', 'patient')")
                ->orderBy('name')->get();
        } else {
            // Secrétaire voit seulement les patients
            $comptes = User::where('role', 'patient')->orderBy('name')->get();
        }

        return view('dashboard', compact('nbRendezvous', 'nbMedecins', 'nbPatients', 'comptes'));
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
