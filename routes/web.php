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
use App\Http\Controllers\TestDownloadController;
use App\Http\Controllers\TempDownloadController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

// Test routes for download system
Route::get('/test/download-model', [TestDownloadController::class, 'testModel']);
Route::get('/test/download-job', [TestDownloadController::class, 'testJob']);

// Route untuk download ZIP QR codes
Route::get('/download/qr-zip/{id}', function ($id) {
    $downloadLog = \App\Models\DownloadLog::where('id', $id)
        ->where('user_id', Auth::id())
        ->where('status', 'completed')
        ->firstOrFail();
    
    if (!$downloadLog->file_path || !Storage::exists($downloadLog->file_path)) {
        abort(404, 'File tidak ditemukan');
    }
    
    return Storage::download($downloadLog->file_path, 'qr-codes-tabung.zip');
})->middleware('auth')->name('download.qr-zip');

// Route untuk download temporary PDF files
Route::get('/download/temp/{filename}', [TempDownloadController::class, 'downloadTempPdf'])
    ->middleware('auth')
    ->name('download.temp.pdf');
