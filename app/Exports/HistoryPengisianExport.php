<?php

namespace App\Exports;

use App\Models\VolumeTabung;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

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

        $records = $query->get();
        
        // Transform data to show each tabung in separate row
        $exportData = new Collection();
        
        foreach ($records as $record) {
            // Calculate total volume per DO
            $totalVolumePerDO = 0;
            if (is_array($record->tabung) && !empty($record->tabung)) {
                foreach ($record->tabung as $tabungItem) {
                    $totalVolumePerDO += $tabungItem['volume'] ?? 0;
                }
            }
            
            if (is_array($record->tabung) && !empty($record->tabung)) {
                foreach ($record->tabung as $tabungItem) {
                    $exportData->push([
                        'id' => $record->id,
                        'tanggal' => $record->tanggal,
                        'lokasi' => $record->lokasi,
                        'nama' => $record->nama,
                        'status' => $record->status,
                        'jumlah_tabung' => count($record->tabung),
                        'kode_tabung' => $tabungItem['kode_tabung'] ?? '-',
                        'volume' => $tabungItem['volume'] ?? 0,
                        'volume_per_do' => $totalVolumePerDO,
                        'keterangan' => $record->keterangan,
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at,
                    ]);
                }
            } else {
                // If no tabung data, still show the record
                $exportData->push([
                    'id' => $record->id,
                    'tanggal' => $record->tanggal,
                    'lokasi' => $record->lokasi,
                    'nama' => $record->nama,
                    'status' => $record->status,
                    'jumlah_tabung' => 0,
                    'kode_tabung' => '-',
                    'volume' => 0,
                    'volume_per_do' => 0,
                    'keterangan' => $record->keterangan,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ]);
            }
        }
        
        return $exportData;
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
            'Kode',
            'Volume',
            'Keterangan',
            'Volume per DO',
            'Dibuat',
            'Diperbarui',
        ];
    }

    public function map($item): array
    {
        // Convert array to object if needed
        $row = is_array($item) ? (object) $item : $item;
        
        return [
            $row->id,
            $row->tanggal ? $row->tanggal->format('d/m/Y') : '-',
            $row->lokasi ?? '-',
            $row->nama ?? '-',
            $row->status ?? '-',
            $row->jumlah_tabung,
            $row->kode_tabung ?? '-',
            $row->volume ?? 0,
            $row->keterangan ?? '-',
            $row->volume_per_do ?? 0,
            $row->created_at ? $row->created_at->format('d/m/Y H:i') : '-',
            $row->updated_at ? $row->updated_at->format('d/m/Y H:i') : '-',
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
            'A2:L1000' => [
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
            // Center align for ID, Status, Jumlah Tabung, Kode, Volume, Volume per DO columns
            'A2:A1000' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'E2:H1000' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'J2:J1000' => [
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
            'C' => 15,  // Lokasi
            'D' => 15,  // Nama Petugas
            'E' => 10,  // Status
            'F' => 15,  // Jumlah Tabung
            'G' => 15,  // Kode
            'H' => 12,  // Volume
            'I' => 30,  // Keterangan
            'J' => 15,  // Volume per DO
            'K' => 18,  // Dibuat
            'L' => 18,  // Diperbarui
        ];
    }
}