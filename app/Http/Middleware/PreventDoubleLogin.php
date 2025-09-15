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
        // Skip middleware untuk semua route auth dan halaman publik
        if (!Auth::check() || 
            $this->shouldSkipRequest($request)) {
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
                
                // Log untuk debugging
                Log::info("Double login detected for user {$userId}. Current: {$currentSessionId}, Stored: {$storedSessionId}");
                
                // Logout user saat ini
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Hapus cache session lama
                Cache::forget($cacheKey);
                
                // Redirect dengan pesan yang lebih friendly
                return redirect()->route('filament.admin.auth.login')
                    ->with('status', 'Akun ini sedang aktif di perangkat lain. Sesi sebelumnya telah diputus.')
                    ->withInput(['email' => $user->email]);
            }
            
            // Update/simpan session ID di cache
            Cache::put($cacheKey, $currentSessionId, now()->addHours(24));
            
        } catch (\Exception $e) {
            // Jika ada error, log dan izinkan request tetap lanjut
            Log::warning('PreventDoubleLogin middleware error: ' . $e->getMessage());
        }
        
        return $next($request);
    }
    
    /**
     * Determine if the request should skip middleware
     */
    private function shouldSkipRequest(Request $request): bool
    {
        $skipPatterns = [
            'login',
            'logout', 
            'register',
            'password',
            'auth',
            'api/',
            '_ignition',
            'livewire',
        ];
        
        $path = $request->path();
        
        foreach ($skipPatterns as $pattern) {
            if (str_contains($path, $pattern)) {
                return true;
            }
        }
        
        // Skip juga untuk route yang mengandung auth
        if ($request->routeIs('filament.*auth*') || 
            $request->routeIs('*login*') ||
            $request->routeIs('*logout*')) {
            return true;
        }
        
        return false;
    }
}