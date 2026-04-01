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
            return redirect()->route('dashboard')->with('error', 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}
