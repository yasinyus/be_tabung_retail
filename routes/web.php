<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TabungController;
use App\Http\Controllers\ArmadaController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\CodeDetailsController;
use App\Http\Controllers\QrCodePdfController;
use App\Http\Controllers\TabungQrCodePdfController;
use App\Http\Controllers\PelangganQrCodePdfController;
use App\Http\Controllers\GudangQrCodePdfController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LaporanPelangganController;

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

// Route untuk mendapatkan detail kode (untuk popup modal)
Route::get('/admin/get-code-details/{code}', [CodeDetailsController::class, 'getDetails'])->name('code.details');

// Route untuk download PDF QR codes
Route::get('/download/armada-qr-codes', [QrCodePdfController::class, 'downloadArmadaQrCodes'])->name('armada.qr-codes.pdf');
Route::get('/download/tabung-qr-codes', [TabungQrCodePdfController::class, 'downloadTabungQrCodes'])->name('tabung.qr-codes.pdf');
Route::get('/download/pelanggan-qr-codes', [PelangganQrCodePdfController::class, 'downloadPelangganQrCodes'])->name('pelanggan.qr-codes.pdf');
Route::get('/download/gudang-qr-codes', [GudangQrCodePdfController::class, 'downloadGudangQrCodes'])->name('gudang.qr-codes.pdf');

// Route untuk download invoice laporan
Route::get('/download/invoice/{id}', [InvoiceController::class, 'downloadInvoice'])->name('laporan.download-invoice');

// Route untuk export laporan pelanggan
Route::get('/export/laporan-pelanggan/pdf', [LaporanPelangganController::class, 'exportPdf'])->name('laporan-pelanggan.export.pdf');
Route::get('/export/laporan-pelanggan/excel', [LaporanPelangganController::class, 'exportExcel'])->name('laporan-pelanggan.export.excel');
