<?php

namespace App\Http\Controllers;

use App\Models\Armada;
use Illuminate\Http\Request;

class ArmadaController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $armada = Armada::findOrFail($id);
            return view('armada.show', compact('armada'));
        } catch (\Exception $e) {
            abort(404, 'Armada tidak ditemukan');
        }
    }
}
