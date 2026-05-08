<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $currentRole = strtolower(trim((string) $request->attributes->get('auth_role', '')));
        $allowed = array_values(array_filter(array_map(
            fn ($role) => strtolower(trim((string) $role)),
            $roles
        )));

        if (empty($allowed) || in_array($currentRole, $allowed, true)) {
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Forbidden',
        ], 403);
    }
}
