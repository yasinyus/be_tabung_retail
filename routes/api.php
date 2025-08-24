<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\MobileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Test endpoint
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now(),
        'version' => 'v1.0'
    ]);
});

// V1 API Group
Route::prefix('v1')->group(function () {
    
    // Authentication routes (no role parameter required)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
    });
    
    // Protected routes requiring authentication
    Route::middleware('auth:sanctum')->group(function () {
        // Dashboard
        Route::get('/dashboard', [MobileController::class, 'dashboard']);
        
        // Data endpoints
        Route::get('/tabung', [MobileController::class, 'getTabung']);
        Route::get('/armada', [MobileController::class, 'getArmada']);
        Route::get('/gudang', [MobileController::class, 'getGudang']);
        Route::get('/pelanggan', [MobileController::class, 'getTabung']); // Note: using getTabung for staff access
        
        // Customer profile (for pelanggan only)
        Route::get('/pelanggan/profile', [MobileController::class, 'getPelangganProfile']);
        
        // QR Scanner
        Route::post('/scan-qr', [MobileController::class, 'scanQr']);
    });
});

// Legacy API endpoints for backward compatibility
Route::post('/login-staff', [AuthController::class, 'loginStaff']);
Route::post('/login-pelanggan', [AuthController::class, 'loginPelanggan']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Legacy protected endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tabung', [MobileController::class, 'getTabung']);
    Route::get('/armada', [MobileController::class, 'getArmada']);
    Route::get('/gudang', [MobileController::class, 'getGudang']);
    Route::get('/pelanggan', [MobileController::class, 'getTabung']); // Note: using getTabung for staff access
    Route::get('/pelanggan/profile', [MobileController::class, 'getPelangganProfile']);
    Route::post('/scan-qr', [MobileController::class, 'scanQr']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
