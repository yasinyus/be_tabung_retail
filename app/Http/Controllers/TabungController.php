<?php

namespace App\Http\Controllers;

use App\Models\Tabung;
use Illuminate\Http\Request;

class TabungController extends Controller
{
    public function show($id)
    {
        $tabung = Tabung::findOrFail($id);
        
        return view('tabung.show', compact('tabung'));
    }
}
