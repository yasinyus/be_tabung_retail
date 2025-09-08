<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HandleUserLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Simpan session ID saat user login
        if ($event->user) {
            $cacheKey = "user_session_{$event->user->id}";
            $sessionId = session()->getId();
            
            // Check jika ada session lama, force logout session lama tersebut
            $oldSessionId = Cache::get($cacheKey);
            if ($oldSessionId && $oldSessionId !== $sessionId) {
                // Optional: Log multiple login attempt
                Log::info("User {$event->user->id} login with new session, old session will be invalidated");
            }
            
            // Simpan session ID yang baru
            Cache::put($cacheKey, $sessionId, now()->addHours(24));
        }
    }
}
