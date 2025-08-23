<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);
            return view('pelanggan.show', compact('pelanggan'));
        } catch (\Exception $e) {
            abort(404, 'Pelanggan tidak ditemukan');
        }
    }
}
