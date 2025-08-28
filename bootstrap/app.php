<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.role' => \App\Http\Middleware\CheckApiRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle unauthenticated redirects for Filament admin
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            // For Filament admin routes, redirect to the correct login route
            if ($request->routeIs('filament.*')) {
                return redirect()->guest(route('filament.admin.auth.login'));
            }
            
            // For API routes, return JSON response
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            
            // Default redirect for web routes (using named route)
            return redirect()->guest(route('filament.admin.auth.login'));
        });
    })->create();
