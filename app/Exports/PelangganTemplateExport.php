<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PelangganTemplateExport implements WithHeadings, WithStyles
{
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'kode_pelanggan',
            'nama_pelanggan',
            'lokasi_pelanggan',
            'harga_tabung',
            'email',
            'password',
            'jenis_pelanggan',
            'penanggung_jawab',
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
