<?php

namespace App\Exports;

use App\Models\LaporanPelanggan;
use App\Models\Pelanggan;
use App\Models\Refund;
use App\Models\StokTabung;
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
    protected $filters;

    public function __construct($kodePelanggan, array $filters = [])
    {
        $this->kodePelanggan = $kodePelanggan;
        $this->pelanggan = Pelanggan::where('kode_pelanggan', $kodePelanggan)->first();
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = LaporanPelanggan::where('kode_pelanggan', $this->kodePelanggan);

        // Apply date filters if provided
        if (!empty($this->filters['dari_tanggal'])) {
            $query->whereDate('tanggal', '>=', $this->filters['dari_tanggal']);
        }

        if (!empty($this->filters['sampai_tanggal'])) {
            $query->whereDate('tanggal', '<=', $this->filters['sampai_tanggal']);
        }

        return $query
            ->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'asc')
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

        // Calculate total volume based on keterangan
        $volumeTotal = 0;
        
        // Jika keterangan = "Tagihan", ambil dari stok_tabung berdasarkan list_tabung
        if ($laporan->keterangan === 'Tagihan' && $laporan->list_tabung) {
            // Parse list_tabung (format: ["kode1", "kode2", ...])
            $listTabung = is_string($laporan->list_tabung) 
                ? json_decode($laporan->list_tabung, true) 
                : $laporan->list_tabung;
            
            if (is_array($listTabung) && count($listTabung) > 0) {
                // Sum volume dari stok_tabung berdasarkan kode_tabung
                $volumeTotal = StokTabung::whereIn('kode_tabung', $listTabung)->sum('volume') ?? 0;
            }
        } 
        // Jika bukan "Tagihan", ambil dari refunds
        elseif (!empty($laporan->id_bast_invoice)) {
            // Sum all volumes from refunds table where bast_id matches
            $volumeTotal = Refund::where('bast_id', $laporan->id_bast_invoice)->sum('volume') ?? 0;
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