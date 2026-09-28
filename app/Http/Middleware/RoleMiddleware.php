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
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $userRole = strtolower(trim((string) $request->user()->role));
        $normalizedRoles = array_map(fn ($r) => strtolower(trim((string) $r)), $roles);

        if (! in_array($userRole, $normalizedRoles, true)) {
            return redirect('/dashboard'); // Redirection si l'utilisateur n'a pas le bon rôle
        }

        return $next($request);
    }
}
