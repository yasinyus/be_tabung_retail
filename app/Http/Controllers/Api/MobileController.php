<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tabung;
use App\Models\Armada;
use App\Models\Gudang;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    /**
     * Dashboard data for mobile app
     * Available for all authenticated users
     */
    public function dashboard(Request $request)
    {
        try {
            $user = $request->user();
            
            // Base data available to all roles
            $data = [
                'user_info' => [
                    'name' => $user->name ?? $user->nama_pelanggan ?? 'Unknown',
                    'email' => $user->email,
                    'user_type' => $user instanceof \App\Models\Pelanggan ? 'pelanggan' : 'staff'
                ]
            ];

            // Add role-specific data
            if ($user instanceof \App\Models\User) {
                $roles = $user->getRoleNames()->toArray();
                $data['user_info']['roles'] = $roles;
                
                // Data for staff roles
                if ($user->hasAnyRole(['kepala_gudang', 'operator', 'driver'])) {
                    $data['stats'] = [
                        'total_tabung' => Tabung::count(),
                        'total_armada' => Armada::count(),
                        'total_gudang' => Gudang::count(),
                        'total_pelanggan' => Pelanggan::count(),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tabung list - for kepala_gudang and operator
     */
    public function getTabung(Request $request)
    {
        try {
            $tabung = Tabung::select('id', 'kode_tabung', 'seri_tabung', 'tahun', 'keterangan', 'qr_code')
                ->orderBy('kode_tabung')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $tabung
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load tabung',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get armada list - for driver and kepala_gudang
     */
    public function getArmada(Request $request)
    {
        try {
            $armada = Armada::select('id', 'nopol', 'kapasitas', 'tahun', 'qr_code')
                ->orderBy('nopol')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $armada
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load armada',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get gudang list - for kepala_gudang and operator
     */
    public function getGudang(Request $request)
    {
        try {
            $gudang = Gudang::select('id', 'kode_gudang', 'nama_gudang', 'tahun_gudang', 'qr_code')
                ->orderBy('kode_gudang')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $gudang
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load gudang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pelanggan profile - for pelanggan role only
     */
    public function getPelangganProfile(Request $request)
    {
        try {
            $pelanggan = $request->user();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pelanggan->id,
                    'kode_pelanggan' => $pelanggan->kode_pelanggan,
                    'nama_pelanggan' => $pelanggan->nama_pelanggan,
                    'email' => $pelanggan->email,
                    'lokasi_pelanggan' => $pelanggan->lokasi_pelanggan,
                    'jenis_pelanggan' => $pelanggan->jenis_pelanggan,
                    'harga_tabung' => $pelanggan->harga_tabung,
                    'penanggung_jawab' => $pelanggan->penanggung_jawab,
                    'qr_code' => $pelanggan->qr_code ? url('pelanggan/' . $pelanggan->id) : null,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * QR Code scanner - get item details by ID
     */
    public function scanQr(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:tabung,armada,gudang,pelanggan',
                'id' => 'required|integer'
            ]);

            $type = $request->type;
            $id = $request->id;

            switch ($type) {
                case 'tabung':
                    $item = Tabung::findOrFail($id);
                    $data = [
                        'type' => 'tabung',
                        'kode_tabung' => $item->kode_tabung,
                        'seri_tabung' => $item->seri_tabung,
                        'tahun' => $item->tahun,
                        'keterangan' => $item->keterangan,
                    ];
                    break;

                case 'armada':
                    $item = Armada::findOrFail($id);
                    $data = [
                        'type' => 'armada',
                        'nopol' => $item->nopol,
                        'kapasitas' => $item->kapasitas,
                        'tahun' => $item->tahun,
                    ];
                    break;

                case 'gudang':
                    $item = Gudang::findOrFail($id);
                    $data = [
                        'type' => 'gudang',
                        'kode_gudang' => $item->kode_gudang,
                        'nama_gudang' => $item->nama_gudang,
                        'tahun_gudang' => $item->tahun_gudang,
                    ];
                    break;

                case 'pelanggan':
                    $item = Pelanggan::findOrFail($id);
                    $data = [
                        'type' => 'pelanggan',
                        'kode_pelanggan' => $item->kode_pelanggan,
                        'nama_pelanggan' => $item->nama_pelanggan,
                        'lokasi_pelanggan' => $item->lokasi_pelanggan,
                        'jenis_pelanggan' => $item->jenis_pelanggan,
                        'harga_tabung' => $item->harga_tabung,
                    ];
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid QR type'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'QR scan failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
