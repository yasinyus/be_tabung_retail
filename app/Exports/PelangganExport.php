<?php

namespace App\Exports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PelangganExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Pelanggan::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Kode Pelanggan',
            'Nama Pelanggan',
            'Lokasi Pelanggan',
            'Harga Tabung',
            'Email',
            'Jenis Pelanggan',
            'Penanggung Jawab',
            'Tanggal Dibuat',
        ];
    }

    /**
     * @param mixed $pelanggan
     * @return array
     */
    public function map($pelanggan): array
    {
        return [
            $pelanggan->id,
            $pelanggan->kode_pelanggan,
            $pelanggan->nama_pelanggan,
            $pelanggan->lokasi_pelanggan,
            $pelanggan->harga_tabung,
            $pelanggan->email,
            $pelanggan->jenis_pelanggan,
            $pelanggan->penanggung_jawab,
            $pelanggan->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
