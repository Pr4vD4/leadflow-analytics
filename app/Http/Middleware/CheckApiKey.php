<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return response()->json([
                'message' => 'API key is missing',
                'status' => 'error'
            ], 403);
        }

        $company = Company::where('api_key', $apiKey)
            ->where('is_active', true)
            ->first();

        if (!$company) {
            return response()->json([
                'message' => 'Invalid API key',
                'status' => 'error'
            ], 403);
        }

        // Store company in request for later use
        $request->attributes->set('company', $company);

        return $next($request);
    }
}
