<?php

namespace App\Http\Controllers;

use App\Models\LaporanPelanggan;
use App\Models\Pelanggan;
use App\Models\Tabung;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function downloadInvoice($id)
    {
        try {
            $laporan = LaporanPelanggan::findOrFail($id);
            $pelanggan = Pelanggan::where('kode_pelanggan', $laporan->kode_pelanggan)->firstOrFail();
            
            // Get list tabung dari field list_tabung di laporan
            $listTabungData = collect();
        
        if ($laporan->list_tabung && is_array($laporan->list_tabung)) {
            // Handle simple array format: ["TB0001", "TB0002", "TB0003"]
            foreach ($laporan->list_tabung as $tabungCode) {
                if (is_string($tabungCode)) {
                    // Ambil detail tabung dari database berdasarkan kode
                    $tabung = Tabung::where('kode_tabung', $tabungCode)->first();
                    
                    if ($tabung) {
                        // Gunakan data dari database
                        $listTabungData->push((object)[
                            'kode_tabung' => $tabung->kode_tabung,
                            'volume' => $tabung->volume ?? '-',
                            'jenis_tabung' => $tabung->jenis_tabung ?? 'Gas LPG',
                            'status_tabung' => 'Terjual',
                            'harga_jual' => $tabung->harga_jual ?? 0,
                            'brand' => $tabung->brand ?? '-',
                        ]);
                    } else {
                        // Jika tabung tidak ditemukan di database, buat data default
                        $listTabungData->push((object)[
                            'kode_tabung' => $tabungCode,
                            'volume' => '12kg',
                            'jenis_tabung' => 'Gas LPG',
                            'status_tabung' => 'Terjual',
                            'harga_jual' => 150000,
                            'brand' => 'Pertamina',
                        ]);
                    }
                }
                // Legacy support: Handle old object format for backward compatibility
                else if (is_array($tabungCode) && isset($tabungCode['kode_tabung'])) {
                    $tabung = Tabung::where('kode_tabung', $tabungCode['kode_tabung'])->first();
                    
                    if ($tabung) {
                        $listTabungData->push((object)[
                            'kode_tabung' => $tabungCode['kode_tabung'],
                            'volume' => $tabungCode['volume'] ?? $tabung->volume ?? '-',
                            'jenis_tabung' => $tabung->jenis_tabung ?? 'Gas LPG',
                            'status_tabung' => 'Terjual',
                            'harga_jual' => $tabungCode['harga'] ?? $tabung->harga_jual ?? 0,
                            'brand' => $tabung->brand ?? '-',
                        ]);
                    } else {
                        $listTabungData->push((object)[
                            'kode_tabung' => $tabungCode['kode_tabung'],
                            'volume' => $tabungCode['volume'] ?? '-',
                            'jenis_tabung' => $tabungCode['jenis'] ?? 'Gas LPG',
                            'status_tabung' => 'Terjual',
                            'harga_jual' => $tabungCode['harga'] ?? 0,
                            'brand' => $tabungCode['brand'] ?? '-',
                        ]);
                    }
                }
            }
        }
        
        $pdf = Pdf::loadView('pdf.invoice', [
            'laporan' => $laporan,
            'pelanggan' => $pelanggan,
            'listTabung' => $listTabungData
        ]);
        
        // Set ukuran dan orientasi PDF
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'invoice-' . $pelanggan->kode_pelanggan . '-' . $laporan->id . '.pdf';
        
        return $pdf->download($filename);
        
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Invoice download error: ' . $e->getMessage());
            
            // Return error response
            return response()->json([
                'error' => 'Gagal mengunduh invoice: ' . $e->getMessage()
            ], 500);
        }
    }
}
