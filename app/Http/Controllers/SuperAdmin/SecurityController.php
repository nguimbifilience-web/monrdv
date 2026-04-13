<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function logs(Request $request)
    {
        $query = ActivityLog::withoutGlobalScopes()->with(['user', 'clinic']);

        if ($request->filled('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        $clinics = Clinic::orderBy('name')->get(['id', 'name']);
        $actions = ActivityLog::withoutGlobalScopes()->distinct()->pluck('action')->filter();

        return view('superadmin.security.logs', compact('logs', 'clinics', 'actions'));
    }

    public function access()
    {
        // Matrice des rôles (read-only) + sessions actives (basique via Session store serait plus complexe)
        $roles = [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin clinique',
            'secretaire' => 'Secrétaire',
            'medecin' => 'Médecin',
            'patient' => 'Patient',
        ];

        $modules = [
            'clinics' => 'Cliniques',
            'users' => 'Utilisateurs',
            'patients' => 'Patients',
            'medecins' => 'Médecins',
            'rendezvous' => 'Rendez-vous',
            'consultations' => 'Consultations',
            'settings' => 'Paramètres globaux',
            'billing' => 'Facturation',
            'security' => 'Sécurité',
        ];

        // Matrice statique basée sur les middlewares existants
        $matrix = [
            'super_admin' => array_fill_keys(array_keys($modules), 'full'),
            'admin' => [
                'clinics' => 'none', 'users' => 'own', 'patients' => 'full',
                'medecins' => 'full', 'rendezvous' => 'full', 'consultations' => 'full',
                'settings' => 'none', 'billing' => 'none', 'security' => 'read',
            ],
            'secretaire' => [
                'clinics' => 'none', 'users' => 'none', 'patients' => 'full',
                'medecins' => 'read', 'rendezvous' => 'full', 'consultations' => 'full',
                'settings' => 'none', 'billing' => 'none', 'security' => 'none',
            ],
            'medecin' => [
                'clinics' => 'none', 'users' => 'none', 'patients' => 'read',
                'medecins' => 'none', 'rendezvous' => 'own', 'consultations' => 'own',
                'settings' => 'none', 'billing' => 'none', 'security' => 'none',
            ],
            'patient' => [
                'clinics' => 'none', 'users' => 'none', 'patients' => 'own',
                'medecins' => 'read', 'rendezvous' => 'own', 'consultations' => 'none',
                'settings' => 'none', 'billing' => 'none', 'security' => 'none',
            ],
        ];

        // Utilisateurs bloqués (par exemple, comptes avec clinique bloquée)
        $lockedClinics = Clinic::where('is_blocked', true)->withCount('users')->get();

        return view('superadmin.security.access', compact('roles', 'modules', 'matrix', 'lockedClinics'));
    }
}
