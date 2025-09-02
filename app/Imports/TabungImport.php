<?php

namespace App\Imports;

use App\Models\Tabung;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TabungImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Tabung([
            'kode_tabung' => $row['kode_tabung'],
            'seri_tabung' => $row['seri_tabung'],
            'tahun' => $row['tahun'] ?? date('Y'),
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_tabung' => 'required|unique:tabungs,kode_tabung',
            'seri_tabung' => 'required',
            'tahun' => 'nullable|integer|min:1980|max:' . (date('Y') + 5),
            'keterangan' => 'nullable|string',
        ];
    }
}
