<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cache;

class ClearUserSessionCache
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
    public function handle(Logout $event): void
    {
        // Clear the user session cache when they logout normally
        if ($event->user) {
            $userId = $event->user->id;
            $cacheKey = "user_session_{$userId}";
            Cache::forget($cacheKey);
        }
    }
}
