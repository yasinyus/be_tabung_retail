<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class StoreUserSessionCache
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Store the current session ID for this user when they login
        if ($event->user) {
            $userId = $event->user->id;
            $currentSessionId = Session::getId();
            $cacheKey = "user_session_{$userId}";
            
            // Check if user already has a session
            $existingSessionId = Cache::get($cacheKey);
            
            if ($existingSessionId && $existingSessionId !== $currentSessionId) {
                // Force logout of previous session by clearing it
                // The middleware will handle the actual logout on the next request
            }
            
            // Store new session ID (expires in 24 hours)
            Cache::put($cacheKey, $currentSessionId, now()->addHours(24));
        }
    }
}
