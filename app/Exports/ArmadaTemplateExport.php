<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArmadaTemplateExport implements WithHeadings, WithStyles
{
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'nopol',
            'kapasitas',
            'tahun',
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
