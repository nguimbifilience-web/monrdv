<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        // Le super admin a accès à toutes les routes
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            $redirect = match($user->role) {
                'medecin' => route('medecin.dashboard'),
                'patient' => route('patient.dashboard'),
                'admin', 'secretaire' => route('dashboard'),
                default => route('login'),
            };
            return redirect($redirect)->with('error', 'Accès non autorisé à cette section.');
        }

        return $next($request);
    }
}
