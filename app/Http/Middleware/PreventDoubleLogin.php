<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PreventDoubleLogin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip untuk route auth dan jika belum login
        if (!Auth::check() || 
            $request->routeIs('filament.*auth*') || 
            str_contains($request->path(), 'login') ||
            str_contains($request->path(), 'logout')) {
            return $next($request);
        }

        try {
            $user = Auth::user();
            $currentSessionId = session()->getId();
            $userId = $user->id;
            
            // Key cache berdasarkan user ID
            $cacheKey = "user_session_{$userId}";
            
            // Ambil session ID yang tersimpan untuk user ini
            $storedSessionId = Cache::get($cacheKey);
            
            // Jika ada session lain untuk user yang sama DAN bukan session yang sama
            if ($storedSessionId && $storedSessionId !== $currentSessionId) {
                // Logout user saat ini
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Hapus cache session lama
                Cache::forget($cacheKey);
                
                // Redirect dengan pesan
                return redirect()->route('filament.admin.auth.login')
                    ->with('status', 'Akun ini sedang digunakan di perangkat lain. Silakan login kembali.')
                    ->with('error', 'Session conflict detected');
            }
            
            // Update session ID di cache
            Cache::put($cacheKey, $currentSessionId, now()->addHours(24));
            
        } catch (\Exception $e) {
            // Jika ada error, izinkan request tetap lanjut
            Log::warning('PreventDoubleLogin middleware error: ' . $e->getMessage());
        }
        
        return $next($request);
    }
}