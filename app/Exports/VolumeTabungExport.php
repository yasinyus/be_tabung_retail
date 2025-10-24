<?php

namespace App\Exports;

use App\Models\StokTabung;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VolumeTabungExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return StokTabung::select('kode_tabung', 'volume', 'status', 'lokasi', 'updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'Kode Tabung',
            'Volume (m3)',
            'Status',
            'Lokasi',
            'Terakhir Update',
        ];
    }
}
