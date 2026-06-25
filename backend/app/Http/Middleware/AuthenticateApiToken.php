<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorization = $request->header('Authorization');

        if (!$authorization) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization header is missing.'
            ], 401);
        }

        if (!str_starts_with($authorization, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization token must be a Bearer token.'
            ], 401);
        }

        $token = substr($authorization, 7);

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization token is empty.'
            ], 401);
        }

        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired authentication token.'
            ], 401);
        }

        // Authenticate the user for the current request lifecycle
        Auth::setUser($user);

        return $next($request);
    }
}
