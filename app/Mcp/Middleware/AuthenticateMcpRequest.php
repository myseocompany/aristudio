<?php

namespace App\Mcp\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMcpRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = config('services.mcp.token');

        if (! is_string($configuredToken) || $configuredToken === '') {
            abort(Response::HTTP_SERVICE_UNAVAILABLE, 'MCP token is not configured.');
        }

        $requestToken = $request->bearerToken();

        if (! is_string($requestToken) || ! hash_equals($configuredToken, $requestToken)) {
            abort(Response::HTTP_UNAUTHORIZED, 'Invalid MCP token.');
        }

        return $next($request);
    }
}
