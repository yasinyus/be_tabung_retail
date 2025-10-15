<?php

namespace App\Exports;

use App\Models\LaporanPelanggan;
use App\Models\Pelanggan;
use App\Models\DetailTransaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPelangganExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $kodePelanggan;
    protected $pelanggan;

    public function __construct($kodePelanggan)
    {
        $this->kodePelanggan = $kodePelanggan;
        $this->pelanggan = Pelanggan::where('kode_pelanggan', $kodePelanggan)->first();
    }

    public function collection()
    {
        return LaporanPelanggan::where('kode_pelanggan', $this->kodePelanggan)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Keterangan',
            'ID BAST Invoice',
            'Jumlah Tabung',
            'Volume Total (m3)',
            'Harga',
            'Deposit (+)',
            'Deposit (-)',
            'Sisa Deposit',
            'Konfirmasi',
            'Dibuat',
        ];
    }

    public function map($laporan): array
    {
        static $no = 1;

        // Get volume total from detail_transaksi by joining with trx_id = id_bast_invoice
        $volumeTotal = 0;
        if (!empty($laporan->id_bast_invoice)) {
            $detailTransaksi = DetailTransaksi::where('trx_id', $laporan->id_bast_invoice)->first();
            
            if ($detailTransaksi && !empty($detailTransaksi->tabung)) {
                $tabungData = $detailTransaksi->tabung;
                
                // Calculate total volume from tabung array
                if (is_array($tabungData)) {
                    foreach ($tabungData as $tabung) {
                        $volumeTotal += $tabung['volume'] ?? 0;
                    }
                }
            }
        }
        
        return [
            $no++,
            $laporan->tanggal ? $laporan->tanggal->format('d/m/Y') : '-',
            $laporan->keterangan ?? '-',
            $laporan->id_bast_invoice ?? '-',
            $laporan->tabung ?? '-',
            number_format($volumeTotal, 2),
            $laporan->harga ? 'Rp ' . number_format($laporan->harga, 0, ',', '.') : '-',
            $laporan->tambahan_deposit ? 'Rp ' . number_format($laporan->tambahan_deposit, 0, ',', '.') : '-',
            $laporan->pengurangan_deposit ? 'Rp ' . number_format($laporan->pengurangan_deposit, 0, ',', '.') : '-',
            $laporan->sisa_deposit ? 'Rp ' . number_format($laporan->sisa_deposit, 0, ',', '.') : '-',
            $laporan->konfirmasi ? 'Ya' : 'Tidak',
            $laporan->created_at->format('d/m/Y H:i'),
        ];
    }

    public function title(): string
    {
        return 'Laporan ' . ($this->pelanggan->nama_pelanggan ?? $this->kodePelanggan);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:L' => ['alignment' => ['horizontal' => 'left']],
        ];
    }
}