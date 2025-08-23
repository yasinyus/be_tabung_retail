<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $gudang = Gudang::findOrFail($id);
            return view('gudang.show', compact('gudang'));
        } catch (\Exception $e) {
            abort(404, 'Gudang tidak ditemukan');
        }
    }
}
