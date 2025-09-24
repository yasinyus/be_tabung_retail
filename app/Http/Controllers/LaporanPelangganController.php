<?php

namespace App\Http\Controllers;

use App\Models\LaporanPelanggan;
use App\Models\Pelanggan;
use App\Exports\LaporanPelangganExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LaporanPelangganController extends Controller
{
    public function exportPdf(Request $request)
    {
        $kodePelanggan = $request->get('kode_pelanggan');
        
        if (!$kodePelanggan) {
            abort(400, 'Kode pelanggan diperlukan');
        }

        $pelanggan = Pelanggan::where('kode_pelanggan', $kodePelanggan)->first();
        
        if (!$pelanggan) {
            abort(404, 'Pelanggan tidak ditemukan');
        }

        $laporans = LaporanPelanggan::where('kode_pelanggan', $kodePelanggan)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-pelanggan', [
            'pelanggan' => $pelanggan,
            'laporans' => $laporans,
            'tanggal_cetak' => now()->format('d/m/Y H:i')
        ]);

        return $pdf->download('Laporan_' . $pelanggan->nama_pelanggan . '_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $kodePelanggan = $request->get('kode_pelanggan');
        
        if (!$kodePelanggan) {
            abort(400, 'Kode pelanggan diperlukan');
        }

        $pelanggan = Pelanggan::where('kode_pelanggan', $kodePelanggan)->first();
        
        if (!$pelanggan) {
            abort(404, 'Pelanggan tidak ditemukan');
        }

        return Excel::download(
            new LaporanPelangganExport($kodePelanggan), 
            'Laporan_' . $pelanggan->nama_pelanggan . '_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}