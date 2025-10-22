<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBackHistory
{
    /**
     * Add headers to prevent browser caching for authenticated pages.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Prevent browsers from caching sensitive pages to avoid stale user info when switching accounts
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}
