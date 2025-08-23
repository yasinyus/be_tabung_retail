<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TabungController;
use App\Http\Controllers\ArmadaController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\PelangganController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk menampilkan detail tabung via QR Code
Route::get('/tabung/{id}', [TabungController::class, 'show'])->name('tabung.show');

// Route untuk menampilkan detail armada via QR Code
Route::get('/armada/{id}', [ArmadaController::class, 'show'])->name('armada.show');

// Route untuk menampilkan detail gudang via QR Code
Route::get('/gudang/{id}', [GudangController::class, 'show'])->name('gudang.show');

// Route untuk menampilkan detail pelanggan via QR Code
Route::get('/pelanggan/{id}', [PelangganController::class, 'show'])->name('pelanggan.show');
