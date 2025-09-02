<?php

namespace App\Imports;

use App\Models\Gudang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GudangImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Gudang([
            'kode_gudang' => $row['kode_gudang'],
            'nama_gudang' => $row['nama_gudang'],
            'tahun_gudang' => $row['tahun_gudang'] ?? date('Y'),
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_gudang' => 'required|unique:gudangs,kode_gudang',
            'nama_gudang' => 'required|string|max:255',
            'tahun_gudang' => 'nullable|integer|min:1980|max:' . (date('Y') + 5),
            'keterangan' => 'nullable|string',
        ];
    }
}
