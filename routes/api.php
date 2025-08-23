<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MobileController;
use App\Http\Controllers\API\TestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication Routes (Public)
Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Test routes
Route::get('test', function() {
    return response()->json([
        'success' => true,
        'message' => 'API Test endpoint working',
        'timestamp' => now(),
        'server' => 'Laravel ' . app()->version()
    ]);
});

// Protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth management
    Route::prefix('v1/auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/refresh', [AuthController::class, 'refreshToken']);
    });
    
    // Legacy user endpoint
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Mobile App API Routes
    Route::prefix('v1/mobile')->group(function () {
        // Dashboard - available for all authenticated users
        Route::get('/dashboard', [MobileController::class, 'dashboard']);
        
        // QR Scanner - available for all authenticated users
        Route::post('/scan-qr', [MobileController::class, 'scanQr']);
        
        // Staff only endpoints
        Route::middleware('api.role:kepala_gudang,operator')->group(function () {
            Route::get('/tabung', [MobileController::class, 'getTabung']);
            Route::get('/gudang', [MobileController::class, 'getGudang']);
        });
        
        // Driver and kepala_gudang endpoints
        Route::middleware('api.role:kepala_gudang,driver')->group(function () {
            Route::get('/armada', [MobileController::class, 'getArmada']);
        });
        
        // Pelanggan only endpoints
        Route::middleware('api.role:pelanggan')->group(function () {
            Route::get('/profile', [MobileController::class, 'getPelangganProfile']);
        });
    });
});

// User Management API Routes (Legacy - Keep for backward compatibility)
Route::prefix('v1')->group(function () {
    // Register endpoint - admin_utama can register new users (no auth required for this example)
    Route::post('/register', [UserController::class, 'register']);
    
    // Protected User CRUD operations - require authentication
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});
