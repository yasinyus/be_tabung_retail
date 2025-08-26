<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\TabungActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Universal login endpoint - auto detect user type (no role parameter needed)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Try to find user in Users table first
        $user = User::where('email', $request->email)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            // Login as User (Admin/Staff)
            $token = $user->createToken('auth-token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user_type' => 'admin',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()->first() ?? 'user',
                ],
                'token' => $token
            ]);
        }

        // Try to find in Pelanggan table
        $pelanggan = Pelanggan::where('email', $request->email)->first();
        
        if ($pelanggan && Hash::check($request->password, $pelanggan->password)) {
            // Login as Pelanggan
            $token = $pelanggan->createToken('auth-token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user_type' => 'pelanggan',
                'user' => [
                    'id' => $pelanggan->id,
                    'name' => $pelanggan->nama_lengkap,
                    'email' => $pelanggan->email,
                    'phone' => $pelanggan->no_telp,
                ],
                'token' => $token
            ]);
        }

        // No user found
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        // Check if it's User model or Pelanggan model
        if ($user instanceof User) {
            return response()->json([
                'status' => 'success',
                'user_type' => 'admin',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()->first() ?? 'user',
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'user_type' => 'pelanggan',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->nama_lengkap,
                    'email' => $user->email,
                    'phone' => $user->no_telp,
                ]
            ]);
        }
    }

    /**
     * Mobile dashboard data
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Basic dashboard data
        return response()->json([
            'status' => 'success',
            'message' => 'Dashboard data retrieved',
            'data' => [
                'user_type' => $user instanceof User ? 'admin' : 'pelanggan',
                'user_name' => $user instanceof User ? $user->name : $user->nama_lengkap,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'server_status' => 'online'
            ]
        ]);
    }

    /**
     * Terima Tabung Armada
     */
    public function terimaTabung(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Validasi input
        $request->validate([
            'lokasi_qr' => 'required|string', // QR Code Gudang
            'armada_qr' => 'required|string', // QR Code Armada
            'tabung_qr' => 'required|array|min:1', // Array QR Code Tabung
            'tabung_qr.*' => 'required|string',
            'keterangan' => 'nullable|string|max:500'
        ]);

        try {
            // Data otomatis
            $tanggal = now()->format('d-m-Y');
            $nama_user = $user instanceof User ? $user->name : $user->nama_lengkap;
            $total_tabung = count($request->tabung_qr);

            // Simulasi validasi QR (nanti bisa disesuaikan dengan database real)
            $lokasi_valid = $this->validateQrCode($request->lokasi_qr, 'gudang');
            $armada_valid = $this->validateQrCode($request->armada_qr, 'armada');
            
            if (!$lokasi_valid) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code Gudang tidak valid'
                ], 400);
            }

            if (!$armada_valid) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code Armada tidak valid'
                ], 400);
            }

            // Validasi setiap QR tabung
            $tabung_invalid = [];
            foreach ($request->tabung_qr as $index => $tabung_qr) {
                if (!$this->validateQrCode($tabung_qr, 'tabung')) {
                    $tabung_invalid[] = $index + 1;
                }
            }

            if (!empty($tabung_invalid)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code Tabung tidak valid',
                    'invalid_tabung' => $tabung_invalid
                ], 400);
            }

            // Simulasi penyimpanan data (nanti bisa disesuaikan dengan model real)
            $transaksi_id = 'TRX-' . now()->format('YmdHis');
            
            // Simpan ke database menggunakan TabungActivity model
            $tabungActivity = TabungActivity::create([
                'activity' => 'Terima Tabung',
                'nama_user' => $nama_user,
                'qr_tabung' => $request->tabung_qr, // JSON array
                'lokasi_gudang' => $request->lokasi_qr,
                'armada' => $request->armada_qr,
                'keterangan' => $request->keterangan,
                'status' => 'Isi', // Fixed: menggunakan nilai yang sesuai dengan ENUM
                'user_id' => $user->id,
                'transaksi_id' => $transaksi_id,
                'tanggal_aktivitas' => now()->format('Y-m-d')
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Data berhasil disimpan! {$total_tabung} tabung telah diterima.",
                'data' => [
                    'transaksi_id' => $tabungActivity->transaksi_id,
                    'tanggal' => $tanggal,
                    'lokasi_qr' => $tabungActivity->lokasi_gudang,
                    'armada_qr' => $tabungActivity->armada,
                    'total_tabung' => $tabungActivity->total_tabung,
                    'nama_user' => $tabungActivity->nama_user,
                    'keterangan' => $tabungActivity->keterangan,
                    'status_transaksi' => $tabungActivity->status,
                    'id_aktivitas' => $tabungActivity->id
                ],
                'notification' => [
                    'title' => 'Tabung Berhasil Diterima',
                    'message' => "Sejumlah {$total_tabung} tabung telah berhasil diterima dari armada.",
                    'type' => 'success'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Validasi QR Code (simulasi - nanti disesuaikan dengan database)
     */
    private function validateQrCode($qr_code, $type)
    {
        // Simulasi validasi QR Code
        // Nanti bisa disesuaikan dengan query database real
        
        switch ($type) {
            case 'gudang':
                // Contoh format QR Gudang: GDG-001, GDG-002, etc.
                return preg_match('/^GDG-\d{3}$/', $qr_code);
                
            case 'armada':
                // Contoh format QR Armada: ARM-001, ARM-002, etc.
                return preg_match('/^ARM-\d{3}$/', $qr_code);
                
            case 'tabung':
                // Contoh format QR Tabung: TBG-001, TBG-002, etc.
                return preg_match('/^TBG-\d{3}$/', $qr_code);
                
            default:
                return false;
        }
    }
}
