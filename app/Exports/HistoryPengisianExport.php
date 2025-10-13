<?php

namespace App\Exports;

use App\Models\VolumeTabung;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class HistoryPengisianExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = VolumeTabung::query()->orderBy('tanggal', 'desc');

        // Apply status filter
        if (isset($this->filters['status']) && !empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Apply lokasi filter
        if (isset($this->filters['lokasi']) && !empty($this->filters['lokasi'])) {
            $query->where('lokasi', $this->filters['lokasi']);
        }

        // Apply keterangan filter (from the custom filter form)
        if (isset($this->filters['keterangan']['search_keterangan']) && !empty($this->filters['keterangan']['search_keterangan'])) {
            $query->where('keterangan', 'like', '%' . $this->filters['keterangan']['search_keterangan'] . '%');
        }

        // Apply date range filters (from the tanggal filter form)
        if (isset($this->filters['tanggal']['dari_tanggal']) && !empty($this->filters['tanggal']['dari_tanggal'])) {
            $query->whereDate('tanggal', '>=', $this->filters['tanggal']['dari_tanggal']);
        }

        if (isset($this->filters['tanggal']['sampai_tanggal']) && !empty($this->filters['tanggal']['sampai_tanggal'])) {
            $query->whereDate('tanggal', '<=', $this->filters['tanggal']['sampai_tanggal']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal Pengisian',
            'Lokasi',
            'Nama Petugas',
            'Status',
            'Jumlah Tabung',
            'Keterangan',
            'Dibuat',
            'Diperbarui',
        ];
    }

    public function map($record): array
    {
        // Hitung jumlah tabung
        $tabungCount = 0;
        if (is_array($record->tabung) && !empty($record->tabung)) {
            $tabungCount = count($record->tabung);
        }

        return [
            $record->id,
            $record->tanggal ? $record->tanggal->format('d/m/Y') : '-',
            $record->lokasi ?? '-',
            $record->nama ?? '-',
            $record->status ?? '-',
            $tabungCount,
            $record->keterangan ?? 'Tidak ada keterangan',
            $record->created_at ? $record->created_at->format('d/m/Y H:i') : '-',
            $record->updated_at ? $record->updated_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['argb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '366092'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            // Style for all data cells
            'A2:I1000' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
            ],
            // Center align for ID, Status, and Jumlah Tabung columns
            'A2:A1000' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'E2:F1000' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 15,  // Tanggal Pengisian
            'C' => 20,  // Lokasi
            'D' => 25,  // Nama Petugas
            'E' => 12,  // Status
            'F' => 15,  // Jumlah Tabung
            'G' => 35,  // Keterangan
            'H' => 18,  // Dibuat
            'I' => 18,  // Diperbarui
        ];
    }
}