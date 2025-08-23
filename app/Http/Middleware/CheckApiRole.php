<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - No user found'
            ], 401);
        }

        // Check if user is a Pelanggan (different handling)
        if ($user instanceof \App\Models\Pelanggan) {
            // For pelanggan, we only check if 'pelanggan' role is required
            if (in_array('pelanggan', $roles)) {
                return $next($request);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Insufficient permissions'
            ], 403);
        }

        // For staff users (User model with Spatie roles)
        if ($user instanceof \App\Models\User) {
            // Check if user has any of the required roles
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    return $next($request);
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized - Insufficient permissions'
        ], 403);
    }
}
