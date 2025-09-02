<?php

namespace App\Imports;

use App\Models\Armada;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ArmadaImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Armada([
            'nopol' => $row['nopol'],
            'kapasitas' => $row['kapasitas'],
            'tahun' => $row['tahun'] ?? date('Y'),
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nopol' => 'required|unique:armadas,nopol',
            'kapasitas' => 'required|integer|min:1',
            'tahun' => 'nullable|integer|min:1980|max:' . (date('Y') + 5),
            'keterangan' => 'nullable|string',
        ];
    }
}
