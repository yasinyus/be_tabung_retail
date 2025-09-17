<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TempDownloadController extends Controller
{
    public function downloadTempPdf($filename)
    {
        // Validate filename to prevent directory traversal
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            abort(400, 'Invalid filename');
        }
        
        $tempPath = storage_path("app/temp/{$filename}");
        
        if (!file_exists($tempPath)) {
            abort(404, 'File tidak ditemukan atau sudah expired');
        }
        
        // Return download response with auto-delete
        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}
