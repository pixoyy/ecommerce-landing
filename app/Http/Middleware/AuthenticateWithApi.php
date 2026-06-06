<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithApi
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasCookie('auth_token')) {
            return $next($request);
        }

        $token = $request->bearerToken();
        if ($token) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
