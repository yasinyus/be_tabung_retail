<?php

namespace App\Imports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PelangganImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Pelanggan([
            'kode_pelanggan' => $row['kode_pelanggan'],
            'nama_pelanggan' => $row['nama_pelanggan'],
            'lokasi_pelanggan' => $row['lokasi_pelanggan'],
            'harga_tabung' => $row['harga_tabung'] ?? 0,
            'email' => $row['email'],
            'password' => bcrypt($row['password'] ?? 'password123'),
            'jenis_pelanggan' => $row['jenis_pelanggan'] ?? 'umum',
            'penanggung_jawab' => $row['penanggung_jawab'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_pelanggan' => 'required|unique:pelanggans,kode_pelanggan',
            'nama_pelanggan' => 'required|string|max:255',
            'lokasi_pelanggan' => 'required|string',
            'harga_tabung' => 'nullable|numeric|min:0',
            'email' => 'required|email|unique:pelanggans,email',
            'jenis_pelanggan' => 'nullable|in:umum,agen',
            'penanggung_jawab' => 'nullable|string|max:255',
        ];
    }
}
