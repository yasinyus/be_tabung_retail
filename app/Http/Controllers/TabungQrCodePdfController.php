<?php

namespace App\Http\Controllers;

use App\Models\Tabung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

class TabungQrCodePdfController extends Controller
{
    public function downloadTabungQrCodes()
    {
        // Ambil semua data tabung
        $tabungs = Tabung::all();
        
        if ($tabungs->isEmpty()) {
            return response('No tabung data found', 404);
        }

        // Generate QR codes untuk setiap tabung
        $qrDataArray = [];
        foreach ($tabungs as $tabung) {
            try {
                // Generate QR code langsung untuk tabung
                $qrContent = json_encode([
                    'id' => $tabung->id,
                    'code' => $tabung->kode_tabung,
                    'url' => url("/tabung/{$tabung->id}")
                ]);

                Log::info("Generating QR for Tabung {$tabung->kode_tabung} with content: " . $qrContent);

                // Coba SVG dulu, jika gagal pakai fallback
                try {
                    $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(150)
                        ->margin(1)
                        ->generate($qrContent);
                    
                    $qrCodeBase64 = base64_encode($qrCodeSvg);
                    
                    Log::info("Generated QR SVG for Tabung {$tabung->kode_tabung}: " . strlen($qrCodeBase64) . ' chars');
                    
                    $qrDataArray[] = [
                        'tabung' => $tabung,
                        'qr_base64' => 'data:image/svg+xml;base64,' . $qrCodeBase64,
                        'qr_text' => $tabung->kode_tabung,
                        'has_qr' => strlen($qrCodeBase64) > 100
                    ];
                } catch (\Exception $svgError) {
                    Log::warning("SVG QR failed for Tabung {$tabung->kode_tabung}, trying default format: " . $svgError->getMessage());
                    
                    // Fallback ke format default
                    $qrCodeDefault = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
                        ->margin(1)
                        ->generate($qrContent);
                    
                    $qrCodeBase64 = base64_encode($qrCodeDefault);
                    
                    Log::info("Generated default QR for Tabung {$tabung->kode_tabung}: " . strlen($qrCodeBase64) . ' chars');
                    
                    $qrDataArray[] = [
                        'tabung' => $tabung,
                        'qr_base64' => 'data:image/svg+xml;base64,' . $qrCodeBase64,
                        'qr_text' => $tabung->kode_tabung,
                        'has_qr' => strlen($qrCodeBase64) > 100
                    ];
                }
                
            } catch (\Exception $e) {
                Log::error("Error generating QR for Tabung {$tabung->kode_tabung}: " . $e->getMessage());
                Log::error("Stack trace: " . $e->getTraceAsString());
                
                // Fallback: No QR code
                $qrDataArray[] = [
                    'tabung' => $tabung,
                    'qr_base64' => null,
                    'qr_text' => $tabung->kode_tabung,
                    'has_qr' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        // Setup DOMPDF dengan konfigurasi yang lebih sederhana
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', false); // Disable remote untuk keamanan
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', realpath(base_path()));
        
        $dompdf = new Dompdf($options);

        // Generate HTML content
        $html = view('pdf.tabung-qr-codes', ['qrData' => collect($qrDataArray)])->render();
        
        // Debug: Save HTML to file for inspection
        file_put_contents(storage_path('app/debug_tabung_qr_pdf.html'), $html);
        Log::info('Tabung PDF HTML saved to: ' . storage_path('app/debug_tabung_qr_pdf.html'));
        
        // Load HTML to DOMPDF
        $dompdf->loadHtml($html);
        
        // Set paper size
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Return PDF download
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="tabung-qr-codes-' . date('Y-m-d') . '.pdf"');
    }
    
    public function downloadTempFile($filename)
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
            
            Log::info("Downloading temp file: {$tempPath}");
            
            // Return download response with auto-delete
            return response()->download($tempPath, $filename, [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error("Error downloading temp file {$filename}: " . $e->getMessage());
            abort(500, 'Error downloading file: ' . $e->getMessage());
        }
    }
}
