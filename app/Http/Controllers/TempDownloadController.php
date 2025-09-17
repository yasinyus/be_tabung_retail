<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TempDownloadController extends Controller
{
    public function downloadTempPdf($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
                Log::warning("Invalid filename attempted: {$filename}");
                abort(400, 'Invalid filename');
            }
            
            $tempPath = storage_path("app/temp/{$filename}");
            
            if (!file_exists($tempPath)) {
                Log::warning("File not found: {$tempPath}");
                abort(404, 'File tidak ditemukan atau sudah expired');
            }
            
            Log::info("Downloading file: {$tempPath}");
            
            // Return download response with auto-delete
            return response()->download($tempPath, $filename, [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error("Error downloading temp file {$filename}: " . $e->getMessage());
            abort(500, 'Error downloading file');
        }
    }
}
