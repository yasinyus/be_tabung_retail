<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// API V1 Routes
Route::prefix('v1')->group(function () {
    
    // Authentication routes (no auth required)
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
    });

    // Mobile routes (auth required)
    Route::prefix('mobile')->middleware('auth:sanctum')->group(function () {
        Route::get('dashboard', [AuthController::class, 'dashboard']);
        Route::post('terima-tabung', [AuthController::class, 'terimaTabung']);
    });

    // Public test route
    Route::get('test', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'API V1 is working!',
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'endpoints' => [
                'auth' => [
                    'POST /api/v1/auth/login',
                    'POST /api/v1/auth/logout',
                    'GET /api/v1/auth/profile'
                ],
                'mobile' => [
                    'GET /api/v1/mobile/dashboard',
                    'POST /api/v1/mobile/terima-tabung'
                ]
            ]
        ]);
    });
});

// Fallback route for API
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'API endpoint not found',
        'available_endpoints' => [
            'GET /api/v1/test',
            'POST /api/v1/auth/login',
            'GET /api/v1/mobile/dashboard (requires auth)'
        ]
    ], 404);
});
