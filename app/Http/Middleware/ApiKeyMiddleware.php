<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Enforce X-API-Key on requests when API_PUBLIC_KEY is configured.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = (string) config('app.api_public_key', env('API_PUBLIC_KEY'));

        // If no key configured, do not enforce (compatibility for dev/local)
        if ($configuredKey === '' || $configuredKey === null) {
            return $next($request);
        }

        $provided = (string) $request->header('X-API-Key', '');
        if (hash_equals($configuredKey, $provided)) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or missing API key.',
        ], 401);
    }
}


