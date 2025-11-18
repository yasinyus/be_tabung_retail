<?php

namespace App\Exports;

use App\Models\TabungActivity;
use App\Models\StokTabung;
use App\Models\Gudang;
use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TabungActivitiesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = TabungActivity::with('user')->orderBy('id', 'desc');
        
        // Apply filters if any
        if (!empty($this->filters['status'])) {
            // Flatten array in case it's nested
            $statuses = is_array($this->filters['status']) ? $this->filters['status'] : [$this->filters['status']];
            $statuses = array_values(array_filter($statuses, function($item) {
                return is_string($item) || is_numeric($item);
            }));
            if (!empty($statuses)) {
                $query->whereIn('status', $statuses);
            }
        }
        
        if (!empty($this->filters['id_user'])) {
            // Flatten array in case it's nested
            $users = is_array($this->filters['id_user']) ? $this->filters['id_user'] : [$this->filters['id_user']];
            $users = array_values(array_filter($users, function($item) {
                return is_string($item) || is_numeric($item);
            }));
            if (!empty($users)) {
                $query->whereIn('id_user', $users);
            }
        }
        
        if (!empty($this->filters['tanggal_dari'])) {
            $query->where('tanggal', '>=', $this->filters['tanggal_dari']);
        }
        
        if (!empty($this->filters['tanggal_sampai'])) {
            $query->where('tanggal', '<=', $this->filters['tanggal_sampai']);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Aktivitas',
            'Nama Petugas',
            'Dari',
            'Tujuan',
            'Status',
            'Jumlah Tabung',
            'Total Volume (m³)',
            'Tanggal',
            'Keterangan',
            'Waktu Input',
        ];
    }

    public function map($activity): array
    {
        // Ambil total volume langsung dari database column
        $totalVolume = $activity->total_volume ?? 0;
        
        // Get display name for "Dari"
        $displayDari = $activity->dari;
        if ($activity->dari) {
            if (str_starts_with($activity->dari, 'GD')) {
                $gudang = Gudang::where('kode_gudang', $activity->dari)->first();
                if ($gudang && $gudang->nama_gudang) {
                    $displayDari = $gudang->nama_gudang;
                }
            } elseif (str_starts_with($activity->dari, 'PA') || str_starts_with($activity->dari, 'PU') || str_starts_with($activity->dari, 'PM')) {
                $pelanggan = Pelanggan::where('kode_pelanggan', $activity->dari)->first();
                if ($pelanggan && $pelanggan->nama_pelanggan) {
                    $displayDari = $pelanggan->nama_pelanggan;
                }
            }
        }
        
        // Get display name for "Tujuan"
        $displayTujuan = $activity->tujuan;
        if ($activity->tujuan) {
            if (str_starts_with($activity->tujuan, 'GD')) {
                $gudang = Gudang::where('kode_gudang', $activity->tujuan)->first();
                if ($gudang && $gudang->nama_gudang) {
                    $displayTujuan = $gudang->nama_gudang;
                }
            } elseif (str_starts_with($activity->tujuan, 'PA') || str_starts_with($activity->tujuan, 'PU') || str_starts_with($activity->tujuan, 'PM')) {
                $pelanggan = Pelanggan::where('kode_pelanggan', $activity->tujuan)->first();
                if ($pelanggan && $pelanggan->nama_pelanggan) {
                    $displayTujuan = $pelanggan->nama_pelanggan;
                }
            }
        }
        
        return [
            $activity->id,
            $activity->nama_aktivitas,
            $activity->nama_petugas,
            $displayDari,
            $displayTujuan,
            $activity->status,
            $activity->total_tabung,
            number_format($totalVolume, 2, '.', ''),
            $activity->tanggal,
            $activity->keterangan ?? '-',
            $activity->waktu ? $activity->waktu->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
