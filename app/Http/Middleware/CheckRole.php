<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirige vers la page de connexion si non authentifié
        }

        // Vérifie si l'utilisateur a l'un des rôles spécifiés
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403); // Accès refusé
        }

        return $next($request);
    }
}
