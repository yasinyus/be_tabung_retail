<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tabung;
use App\Models\Gudang;
use App\Models\Pelanggan;
use App\Models\Armada;

class CodeDetailsController extends Controller
{
    /**
     * Get details for a code (T-001, GDG-001, PLG-001, or license plate)
     */
    public function getDetails($code)
    {
        $code = trim($code);
        $details = $this->getCodeDetails($code);
        
        if ($details) {
            return response()->json([
                'success' => true,
                'title' => $details['title'],
                'content' => $this->generatePopupContent($details),
                'type' => $details['type']
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Detail tidak ditemukan untuk kode: ' . $code
        ]);
    }
    
    /**
     * Get details for a code (T-001, GDG-001, PLG-001, or license plate)
     */
    protected function getCodeDetails(string $code): ?array
    {
        // Check for Tabung (T-001)
        if (preg_match('/^T-\d+$/', $code)) {
            $tabung = Tabung::where('kode_tabung', $code)->first();
            if ($tabung) {
                return [
                    'type' => 'Tabung',
                    'title' => "Detail Tabung - {$tabung->kode_tabung}",
                    'details' => [
                        'Kode Tabung' => $tabung->kode_tabung,
                        'Seri Tabung' => $tabung->seri_tabung ?? 'Tidak ada',
                        'Tahun' => $tabung->tahun ?? 'Tidak ada',
                        'Keterangan' => $tabung->keterangan ?? 'Tidak ada keterangan',
                    ]
                ];
            }
        }
        
        // Check for Gudang (GDG-001)
        if (preg_match('/^GDG-\d+$/', $code)) {
            $gudang = Gudang::where('kode_gudang', $code)->first();
            if ($gudang) {
                return [
                    'type' => 'Gudang',
                    'title' => "Detail Gudang - {$gudang->kode_gudang}",
                    'details' => [
                        'Kode Gudang' => $gudang->kode_gudang,
                        'Nama Gudang' => $gudang->nama_gudang ?? 'Tidak ada',
                        'Tahun Gudang' => $gudang->tahun_gudang ?? 'Tidak ada',
                        'Keterangan' => $gudang->keterangan ?? 'Tidak ada',
                    ]
                ];
            }
        }
        
        // Check for Pelanggan (PLG-001)
        if (preg_match('/^PLG-\d+$/', $code)) {
            $pelanggan = Pelanggan::where('kode_pelanggan', $code)->first();
            if ($pelanggan) {
                return [
                    'type' => 'Pelanggan',
                    'title' => "Detail Pelanggan - {$pelanggan->kode_pelanggan}",
                    'details' => [
                        'Kode Pelanggan' => $pelanggan->kode_pelanggan,
                        'Nama Pelanggan' => $pelanggan->nama_pelanggan ?? 'Tidak ada',
                        'Email' => $pelanggan->email ?? 'Tidak ada',
                        'Jenis Pelanggan' => ucfirst($pelanggan->jenis_pelanggan ?? 'Tidak ada'),
                        'Lokasi' => $pelanggan->lokasi_pelanggan ?? 'Tidak ada',
                        'Harga Tabung' => $pelanggan->harga_tabung ? 'Rp ' . number_format($pelanggan->harga_tabung, 0, ',', '.') : 'Tidak ada',
                    ]
                ];
            }
        }
        
        // Check for Armada (license plate)
        $armada = Armada::where('nopol', $code)->first();
        if ($armada) {
            return [
                'type' => 'Armada',
                'title' => "Detail Armada - {$armada->nopol}",
                'details' => [
                    'No. Polisi' => $armada->nopol,
                    'Kapasitas' => $armada->kapasitas ?? 'Tidak ada',
                    'Tahun' => $armada->tahun ?? 'Tidak ada',
                    'Keterangan' => $armada->keterangan ?? 'Tidak ada',
                ]
            ];
        }
        
        return null;
    }

    /**
     * Generate popup content HTML
     */
    protected function generatePopupContent(array $details): string
    {
        $html = '';
        
        foreach ($details['details'] as $label => $value) {
            $html .= "<div class='mb-3 flex code-detail-row' style='margin-bottom: 12px; display: flex;'>";
            $html .= "<div class='w-1/3 detail-label' style='width: 33.333333%; padding-right: 12px;'><strong style='font-weight: 600;'>{$label}:</strong></div>";
            $html .= "<div class='w-2/3 detail-value' style='width: 66.666667%;'>{$value}</div>";
            $html .= "</div>";
        }
        
        return $html;
    }
}
