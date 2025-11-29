<?php

namespace App\Exports;

use App\Models\StokTabung;
use App\Models\Gudang;
use App\Models\Pelanggan;
use App\Models\Armada;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VolumeTabungExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    public function collection()
    {
        $query = StokTabung::query()
            ->leftJoin('gudangs', function($join) {
                $join->on('stok_tabung.lokasi', '=', 'gudangs.kode_gudang')
                     ->where('stok_tabung.lokasi', 'like', 'GD%');
            })
            ->leftJoin('pelanggans', function($join) {
                $join->on('stok_tabung.lokasi', '=', 'pelanggans.kode_pelanggan')
                     ->where(function($query) {
                         $query->where('stok_tabung.lokasi', 'like', 'PU%')
                               ->orWhere('stok_tabung.lokasi', 'like', 'PA%')
                               ->orWhere('stok_tabung.lokasi', 'like', 'PM%');
                     });
            })
            ->leftJoin('armadas', function($join) {
                $join->on('stok_tabung.lokasi', '=', 'armadas.nopol');
            })
            ->select(
                'stok_tabung.kode_tabung',
                'stok_tabung.volume',
                'stok_tabung.status',
                'stok_tabung.lokasi',
                'gudangs.nama_gudang',
                'pelanggans.nama_pelanggan',
                'armadas.nopol as armada_nopol',
                'stok_tabung.updated_at'
            );

        // Apply filters
        $filters = $this->filters ?? [];

        // Helper to read filter value whether provided as a scalar or an array with 'value'
        $getFilterValue = function ($key) use ($filters) {
            if (!isset($filters[$key])) {
                return null;
            }
            $val = $filters[$key];
            if (is_array($val) && isset($val['value'])) {
                return $val['value'];
            }
            return $val;
        };

        // Lokasi filter (kode keur)
        $lokasiVal = $getFilterValue('lokasi');
        if (!empty($lokasiVal)) {
            $query->where('stok_tabung.lokasi', $lokasiVal);
        }

        // Status filter
        $statusVal = $getFilterValue('status');
        if (!empty($statusVal)) {
            // Allow for either a string or an array of statuses
            $statuses = is_array($statusVal) ? $statusVal : [$statusVal];
            $statuses = array_values(array_filter($statuses, function($item) {
                return is_string($item) || is_numeric($item);
            }));
            if (!empty($statuses)) {
                $query->whereIn('stok_tabung.status', $statuses);
            }
        }

        // Volume filter
        $volumeFilter = $getFilterValue('volume_filter');
        if ($volumeFilter === 'bervolume') {
            $query->whereNotNull('stok_tabung.volume')->where('stok_tabung.volume', '>', 0);
        } elseif ($volumeFilter === 'tanpa_volume') {
            $query->whereNull('stok_tabung.volume')->orWhere('stok_tabung.volume', 0);
        }

        return $query->get();
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

    public function map($stok): array
    {
        $lokasiNama = $stok->nama_gudang ?? $stok->nama_pelanggan ?? $stok->armada_nopol ?? $stok->lokasi;

        return [
            $stok->kode_tabung,
            number_format((float)$stok->volume, 2, '.', ''),
            $stok->status,
            $lokasiNama,
            $stok->updated_at ? $stok->updated_at->format('d/m/Y H:i') : '-',
        ];
    }
}
