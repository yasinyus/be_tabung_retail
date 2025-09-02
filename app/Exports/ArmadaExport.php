<?php

namespace App\Exports;

use App\Models\Armada;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArmadaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Armada::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nomor Polisi',
            'Kapasitas (ton)',
            'Tahun',
            'Keterangan',
            'Tanggal Dibuat',
        ];
    }

    /**
     * @param mixed $armada
     * @return array
     */
    public function map($armada): array
    {
        return [
            $armada->id,
            $armada->nopol,
            $armada->kapasitas,
            $armada->tahun,
            $armada->keterangan,
            $armada->created_at->format('Y-m-d H:i:s'),
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
