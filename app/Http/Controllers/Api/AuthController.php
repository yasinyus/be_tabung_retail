<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login API for mobile application
     * Supports multiple user types: User (with roles) and Pelanggan
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'role' => 'required|in:kepala_gudang,operator,driver,pelanggan'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $email = $request->email;
            $password = $request->password;
            $role = $request->role;

            if (in_array($role, ['kepala_gudang', 'operator', 'driver'])) {
                // Login for staff users
                return $this->loginStaff($email, $password, $role);
            } else {
                // Login for pelanggan
                return $this->loginPelanggan($email, $password);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login for staff users (kepala_gudang, operator, driver)
     */
    private function loginStaff($email, $password, $expectedRole)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check if user has the expected role
        if (!$user->hasRole($expectedRole)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized role'
            ], 403);
        }

        // Create token
        $token = $user->createToken('mobile-app-' . $user->id)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $expectedRole,
                    'created_at' => $user->created_at,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 200);
    }

    /**
     * Login for pelanggan users
     */
    private function loginPelanggan($email, $password)
    {
        $pelanggan = Pelanggan::where('email', $email)->first();

        if (!$pelanggan || !Hash::check($password, $pelanggan->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Create token for pelanggan (using User model for token generation)
        // We need to create a temporary user or use a different approach
        $token = $pelanggan->createToken('mobile-app-pelanggan-' . $pelanggan->id)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $pelanggan->id,
                    'name' => $pelanggan->nama_pelanggan,
                    'email' => $pelanggan->email,
                    'role' => 'pelanggan',
                    'kode_pelanggan' => $pelanggan->kode_pelanggan,
                    'lokasi_pelanggan' => $pelanggan->lokasi_pelanggan,
                    'jenis_pelanggan' => $pelanggan->jenis_pelanggan,
                    'harga_tabung' => $pelanggan->harga_tabung,
                    'penanggung_jawab' => $pelanggan->penanggung_jawab,
                    'created_at' => $pelanggan->created_at,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 200);
    }

    /**
     * Logout API
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            
            // Check if it's a staff user or pelanggan
            if ($user instanceof User) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_type' => 'staff',
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'roles' => $user->getRoleNames()->toArray(),
                            'created_at' => $user->created_at,
                        ]
                    ]
                ], 200);
            } else {
                // Handle pelanggan profile
                $pelanggan = Pelanggan::where('email', $user->email)->first();
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_type' => 'pelanggan',
                        'user' => [
                            'id' => $pelanggan->id,
                            'name' => $pelanggan->nama_pelanggan,
                            'email' => $pelanggan->email,
                            'kode_pelanggan' => $pelanggan->kode_pelanggan,
                            'lokasi_pelanggan' => $pelanggan->lokasi_pelanggan,
                            'jenis_pelanggan' => $pelanggan->jenis_pelanggan,
                            'harga_tabung' => $pelanggan->harga_tabung,
                            'penanggung_jawab' => $pelanggan->penanggung_jawab,
                            'created_at' => $pelanggan->created_at,
                        ]
                    ]
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        try {
            $user = $request->user();
            
            // Delete current token
            $request->user()->currentAccessToken()->delete();
            
            // Create new token
            $newToken = $user->createToken('mobile-app-' . $user->id)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'token' => $newToken,
                    'token_type' => 'Bearer'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
