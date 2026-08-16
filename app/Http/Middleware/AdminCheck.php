<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminCheck
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $roles = ['superadmin', 'admin', 'moder', 'moderator'];

        if (!$user || !$user->is_active || !in_array(strtolower($user->role), $roles)) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return $next($request);
    }
}
