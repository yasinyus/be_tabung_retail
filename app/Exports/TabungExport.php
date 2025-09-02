<?php

namespace App\Exports;

use App\Models\Tabung;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TabungExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Tabung::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Kode Tabung',
            'Seri Tabung',
            'Tahun',
            'Keterangan',
            'Tanggal Dibuat',
        ];
    }

    /**
     * @param mixed $tabung
     * @return array
     */
    public function map($tabung): array
    {
        return [
            $tabung->id,
            $tabung->kode_tabung,
            $tabung->seri_tabung,
            $tabung->tahun,
            $tabung->keterangan,
            $tabung->created_at->format('Y-m-d H:i:s'),
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
