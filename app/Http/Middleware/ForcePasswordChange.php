<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force l'utilisateur a changer son mot de passe au premier login si
 * son flag `must_change_password` est true.
 *
 * Les routes autorisees pendant le blocage :
 *  - profile.edit / profile.update (pour changer effectivement le MDP)
 *  - logout
 *  - password.* (reset flow Breeze)
 */
class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password && !$this->isAllowedRoute($request)) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Veuillez changer votre mot de passe temporaire avant de continuer.');
        }

        return $next($request);
    }

    private function isAllowedRoute(Request $request): bool
    {
        $name = optional($request->route())->getName();

        if (!$name) {
            return false;
        }

        return in_array($name, ['profile.edit', 'profile.update', 'logout'], true)
            || str_starts_with($name, 'password.');
    }
}
