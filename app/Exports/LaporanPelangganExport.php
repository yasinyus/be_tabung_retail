<?php

namespace App\Exports;

use App\Models\LaporanPelanggan;
use App\Models\Pelanggan;
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
        
        return [
            $no++,
            $laporan->tanggal ? $laporan->tanggal->format('d/m/Y') : '-',
            $laporan->keterangan ?? '-',
            $laporan->id_bast_invoice ?? '-',
            $laporan->tabung ?? '-',
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
            'A:K' => ['alignment' => ['horizontal' => 'left']],
        ];
    }
}