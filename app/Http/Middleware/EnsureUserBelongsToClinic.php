<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToClinic
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Laisser passer les visiteurs non connectés (le middleware auth gère ça)
        if (!$user) {
            return $next($request);
        }

        // Le super admin peut tout voir
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Bloquer les utilisateurs sans clinique
        if (!$user->clinic_id) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte n\'est rattaché à aucune clinique. Contactez l\'administrateur.',
            ]);
        }

        // Vérifier que la clinique est active
        if ($user->clinic && !$user->clinic->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Votre clinique est actuellement désactivée. Contactez l\'administrateur.',
            ]);
        }

        // Vérifier que la clinique n'est pas bloquée (non-paiement)
        if ($user->clinic && $user->clinic->is_blocked) {
            $slug = $user->clinic->slug;
            auth()->logout();
            $request->session()->invalidate();
            return redirect()->route('clinic.blocked', ['clinic' => $slug]);
        }

        // Vérifier les paramètres de route (model binding)
        foreach ($request->route()->parameters() as $param) {
            if (is_object($param) && method_exists($param, 'getAttribute')) {
                $clinicId = $param->getAttribute('clinic_id');
                if ($clinicId && $clinicId !== $user->clinic_id) {
                    abort(403, 'Accès interdit : cette ressource appartient à une autre clinique.');
                }
            }
        }

        return $next($request);
    }
}
