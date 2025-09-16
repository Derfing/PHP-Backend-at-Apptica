<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LogRequests
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            Log::info('Incoming request', [
                'ip' => $request->ip(),
                'method' => $request->method(),
                'path' => $request->path(),
                'query' => $request->query(),
                'status' => $response->getStatusCode(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log request', ['error' => $e->getMessage()]);
        }

        return $response;
    }
}
