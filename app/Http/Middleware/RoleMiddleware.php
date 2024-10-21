<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Ajout de l'importation de la façade Auth

class Rolemiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response // Ajout d'une valeur par défaut pour $roles
        {

        // Vérifiez si l'utilisateur est authentifié
        if (Auth::check() && !in_array(Auth::user()->role, $roles)) { // Modification ici
            return redirect()->route('login');
        }
        return $next($request);
    }
}
