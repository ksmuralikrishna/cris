<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tablet;

class AuthenticateTablet
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $tablet = Tablet::where('api_token', $token)->where('is_active', true)->first();

        if (!$tablet) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->merge(['_tablet' => $tablet]);

        return $next($request);
    }
}
