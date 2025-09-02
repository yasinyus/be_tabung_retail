<?php

namespace App\Exports;

use App\Models\Gudang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GudangExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Gudang::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Kode Gudang',
            'Nama Gudang',
            'Tahun Gudang',
            'Keterangan',
            'Tanggal Dibuat',
        ];
    }

    /**
     * @param mixed $gudang
     * @return array
     */
    public function map($gudang): array
    {
        return [
            $gudang->id,
            $gudang->kode_gudang,
            $gudang->nama_gudang,
            $gudang->tahun_gudang,
            $gudang->keterangan,
            $gudang->created_at->format('Y-m-d H:i:s'),
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
