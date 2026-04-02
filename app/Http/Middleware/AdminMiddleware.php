<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && !$user->isSuperAdmin())) {
            // Rediriger vers le dashboard approprié selon le rôle
            $redirect = match($user?->role) {
                'medecin' => route('medecin.dashboard'),
                'patient' => route('patient.dashboard'),
                'secretaire' => route('dashboard'),
                default => route('login'),
            };
            return redirect($redirect)->with('error', 'Accès réservé aux administrateurs.');
        }

        // Vérifier que l'admin a bien une clinique assignée (sauf super_admin)
        if ($user->isAdmin() && !$user->clinic_id) {
            abort(403, 'Votre compte n\'est rattaché à aucune clinique.');
        }

        return $next($request);
    }
}
