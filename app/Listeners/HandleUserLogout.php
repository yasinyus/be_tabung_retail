<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cache;

class HandleUserLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        // Hapus session dari cache saat user logout
        if ($event->user) {
            $cacheKey = "user_session_{$event->user->id}";
            Cache::forget($cacheKey);
        }
    }
}
