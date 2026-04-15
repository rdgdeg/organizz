<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Empêche le navigateur de réutiliser une ancienne page HTML (souvent la cause de « vieux » JS / Inertia en prod).
 * Les assets Vite restent hashés ; ici on cible surtout le document shell.
 */
class PreventBrowserCachingForAuthenticatedUsers
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->user()) {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        if ($response->isSuccessful() && str_contains($contentType, 'text/html')) {
            $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
