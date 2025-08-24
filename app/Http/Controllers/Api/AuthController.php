<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pelanggan;

class AuthController extends Controller
{
    /**
     * Handle login request without role parameter
     * Automatically detects user type and role
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Try to authenticate as Staff (User) first
        $user = User::where('email', $request->email)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            // Create token for staff user
            $token = $user->createToken('auth-token')->plainTextToken;
            
            // Get user role (auto-detected from database)
            $userRoles = $user->getRoleNames();
            $primaryRole = $userRoles->isNotEmpty() ? $userRoles->first() : 'user';
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $primaryRole, // Auto-detected role
                ],
                'token' => $token
            ], 200);
        }

        // Try to authenticate as Customer (Pelanggan)
        $pelanggan = Pelanggan::where('email', $request->email)->first();
        
        if ($pelanggan && Hash::check($request->password, $pelanggan->password)) {
            // Create token for customer
            $token = $pelanggan->createToken('auth-token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => [
                    'id' => $pelanggan->id,
                    'name' => $pelanggan->nama_pelanggan,
                    'email' => $pelanggan->email,
                    'kode_pelanggan' => $pelanggan->kode_pelanggan,
                    'lokasi_pelanggan' => $pelanggan->lokasi_pelanggan,
                    'jenis_pelanggan' => $pelanggan->jenis_pelanggan,
                    'role' => 'pelanggan', // Fixed role for customers
                ],
                'token' => $token
            ], 200);
        }

        // Authentication failed
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        try {
            // Delete current access token
            $request->user()->currentAccessToken()->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user instanceof User) {
                // Staff user
                $userRoles = $user->getRoleNames();
                $primaryRole = $userRoles->isNotEmpty() ? $userRoles->first() : 'user';
                
                return response()->json([
                    'status' => 'success',
                    'user_type' => 'staff',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $primaryRole,
                    ]
                ], 200);
                
            } elseif ($user instanceof Pelanggan) {
                // Customer user
                return response()->json([
                    'status' => 'success',
                    'user_type' => 'customer',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->nama_pelanggan,
                        'email' => $user->email,
                        'kode_pelanggan' => $user->kode_pelanggan,
                        'lokasi_pelanggan' => $user->lokasi_pelanggan,
                        'jenis_pelanggan' => $user->jenis_pelanggan,
                        'role' => 'pelanggan',
                    ]
                ], 200);
            }
            
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Legacy methods for backward compatibility
    
    /**
     * Staff login (for backward compatibility)
     */
    public function loginStaff(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        return $this->authenticateStaff($request->email, $request->password);
    }

    /**
     * Customer login (for backward compatibility)
     */
    public function loginPelanggan(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        return $this->authenticatePelanggan($request->email, $request->password);
    }

    /**
     * Private method to authenticate staff
     */
    private function authenticateStaff($email, $password)
    {
        $user = User::where('email', $email)->first();
        
        if ($user && Hash::check($password, $user->password)) {
            $token = $user->createToken('auth-token')->plainTextToken;
            
            $userRoles = $user->getRoleNames();
            $primaryRole = $userRoles->isNotEmpty() ? $userRoles->first() : 'user';
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $primaryRole,
                ],
                'token' => $token
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Private method to authenticate customer
     */
    private function authenticatePelanggan($email, $password)
    {
        $pelanggan = Pelanggan::where('email', $email)->first();
        
        if ($pelanggan && Hash::check($password, $pelanggan->password)) {
            $token = $pelanggan->createToken('auth-token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => [
                    'id' => $pelanggan->id,
                    'name' => $pelanggan->nama_pelanggan,
                    'email' => $pelanggan->email,
                    'kode_pelanggan' => $pelanggan->kode_pelanggan,
                    'lokasi_pelanggan' => $pelanggan->lokasi_pelanggan,
                    'jenis_pelanggan' => $pelanggan->jenis_pelanggan,
                ],
                'token' => $token
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials'
        ], 401);
    }
}
