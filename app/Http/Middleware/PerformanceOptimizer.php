<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PerformanceOptimizer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Optimisations pour les routes de connexion/autentification
        if ($this->isAuthRoute($request)) {
            return $this->optimizeAuthResponse($next($request));
        }

        // Optimisations générales pour les autres routes
        return $this->optimizeGeneralResponse($next($request));
    }

    /**
     * Vérifie si la requête concerne une route d'authentification
     */
    private function isAuthRoute(Request $request): bool
    {
        $authRoutes = ['login', 'custom.login', 'password.request', 'password.reset'];

        return in_array($request->route()?->getName(), $authRoutes) ||
               str_starts_with($request->path(), 'login') ||
               str_starts_with($request->path(), 'password');
    }

    /**
     * Optimise les réponses d'authentification
     */
    private function optimizeAuthResponse(Response $response): Response
    {
        // Cache les assets pour les pages de connexion
        if ($response->isOk() && $this->shouldCacheAssets($response)) {
            $response->setCache([
                'max_age' => 3600, // 1 heure
                's_maxage' => 7200, // 2 heures pour les proxies
                'public' => true,
            ]);
        }

        // Ajouter les en-têtes de performance
        $response->headers->set('X-Accel-Expires', '@60m'); // Nginx cache 60 min

        return $response;
    }

    /**
     * Optimise les réponses générales
     */
    private function optimizeGeneralResponse(Response $response): Response
    {
        // Compression et cache pour les assets statiques
        if ($this->isStaticAsset($response)) {
            $response->setCache([
                'max_age' => 86400, // 24 heures
                's_maxage' => 259200, // 3 jours pour les proxies
                'public' => true,
                'immutable' => true,
            ]);

            $response->headers->set('Cache-Control', 'public, max-age=86400, immutable');
        }

        // Headers de sécurité et performance
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }

    /**
     * Vérifie si la réponse devrait être mise en cache pour les assets
     */
    private function shouldCacheAssets(Response $response): bool
    {
        return config('app.cache_headers', true) &&
               ($response->isOk() || $response->isRedirect());
    }

    /**
     * Vérifie si la réponse concerne un asset statique
     */
    private function isStaticAsset(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/css') ||
               str_contains($contentType, 'application/javascript') ||
               str_contains($contentType, 'image/') ||
               str_contains($contentType, 'font/');
    }
}
