<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TabungTemplateExport implements WithHeadings, WithStyles
{
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'kode_tabung',
            'seri_tabung',
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
