<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GudangTemplateExport implements WithHeadings, WithStyles
{
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'kode_gudang',
            'nama_gudang',
            'tahun_gudang',
            'keterangan',
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
