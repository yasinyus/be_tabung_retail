<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // We'll use route middleware instead of controller middleware

    /**
     * Display a listing of the resource.
     * Hanya admin_utama yang bisa akses
     */
    public function index(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user || !$user->hasRole('admin_utama')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admin_utama can access this resource.',
            ], 403);
        }

        $users = User::with('roles')->latest()->get();
        
        // Log activity
        Log::info('User list accessed', ['accessed_by' => $user->email]);

        return response()->json([
            'success' => true,
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'roles' => $user->roles->pluck('name'),
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];
            }),
        ]);
    }

    /**
     * Register/Store a newly created user.
     * For demo purposes, this endpoint doesn't require auth
     * In production, you might want to require admin authentication
     */
    public function register(Request $request): JsonResponse
    {
        // Check if user is authenticated and has admin role
        if (Auth::check()) {
            /** @var \App\Models\User $currentUser */
            $currentUser = Auth::user();
            
            if (!$currentUser->hasRole('admin_utama')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin_utama can create users.',
                ], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin_utama,admin_umum,kepala_gudang,operator_retail,driver,auditor,pelanggan_umum,pelanggan_agen',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Assign role menggunakan Spatie Permission
        $user->assignRole($request->role);

        // Log activity
        if (Auth::check()) {
            /** @var \App\Models\User $currentUser */
            $currentUser = Auth::user();
            Log::info('New user created via API', [
                'created_by' => $currentUser->email,
                'new_user' => $user->email,
                'role' => $user->role,
            ]);
        } else {
            Log::info('New user registered via API', [
                'new_user' => $user->email,
                'role' => $user->role,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        
        if (!$currentUser || !$currentUser->hasRole('admin_utama')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admin_utama can access this resource.',
            ], 403);
        }

        $user = User::with('roles')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'roles' => $user->roles->pluck('name'),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        
        if (!$currentUser || !$currentUser->hasRole('admin_utama')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admin_utama can update users.',
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'role' => 'sometimes|required|in:admin_utama,admin_umum,kepala_gudang,operator_retail,driver,auditor,pelanggan_umum,pelanggan_agen',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = $request->only(['name', 'email', 'role']);
        
        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Update role jika ada perubahan
        if ($request->has('role')) {
            $user->syncRoles([]);
            $user->assignRole($request->role);
        }

        // Log activity
        Log::info('User updated via API', [
            'updated_by' => $currentUser->email,
            'updated_user' => $user->email,
            'changes' => $updateData,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'updated_at' => $user->fresh()->updated_at,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        
        if (!$currentUser || !$currentUser->hasRole('admin_utama')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admin_utama can delete users.',
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Prevent admin from deleting themselves
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        // Log activity before deletion
        Log::info('User deleted via API', [
            'deleted_by' => $currentUser->email,
            'deleted_user' => $user->email,
            'deleted_user_role' => $user->role,
        ]);

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
