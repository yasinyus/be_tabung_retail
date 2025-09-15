<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Login;
use App\Listeners\HandleUserLogout;
use App\Listeners\HandleUserLogin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Only register Pail in local environment and if class exists
        if (app()->environment("local") && class_exists(\Laravel\Pail\PailServiceProvider::class)) {
            $this->app->register(\Laravel\Pail\PailServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register login event listener - TEMPORARILY DISABLED
        /*
        Event::listen(
            Login::class,
            HandleUserLogin::class,
        );
        
        // Register logout event listener
        Event::listen(
            Logout::class,
            HandleUserLogout::class,
        );
        */
    }
}
