<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

class GudangQrCodePdfController extends Controller
{
    public function downloadGudangQrCodes()
    {
        // Ambil semua data gudang
        $gudangs = Gudang::all();
        
        if ($gudangs->isEmpty()) {
            return response('No gudang data found', 404);
        }

        // Generate QR codes untuk setiap gudang
        $qrDataArray = [];
        foreach ($gudangs as $gudang) {
            try {
                // Generate QR code langsung untuk gudang
                $qrContent = json_encode([
                    'id' => $gudang->id,
                    'code' => $gudang->kode_gudang,
                    'url' => url("/gudang/{$gudang->id}")
                ]);

                Log::info("Generating QR for Gudang {$gudang->kode_gudang} with content: " . $qrContent);

                // Coba SVG dulu, jika gagal pakai fallback
                try {
                    $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(150)
                        ->margin(1)
                        ->generate($qrContent);
                    
                    $qrCodeBase64 = base64_encode($qrCodeSvg);
                    
                    Log::info("Generated QR SVG for Gudang {$gudang->kode_gudang}: " . strlen($qrCodeBase64) . ' chars');
                    
                    $qrDataArray[] = [
                        'gudang' => $gudang,
                        'qr_base64' => 'data:image/svg+xml;base64,' . $qrCodeBase64,
                        'qr_text' => $gudang->kode_gudang,
                        'has_qr' => strlen($qrCodeBase64) > 100
                    ];
                } catch (\Exception $svgError) {
                    Log::warning("SVG QR failed for Gudang {$gudang->kode_gudang}, trying default format: " . $svgError->getMessage());
                    
                    // Fallback ke format default
                    $qrCodeDefault = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
                        ->margin(1)
                        ->generate($qrContent);
                    
                    $qrCodeBase64 = base64_encode($qrCodeDefault);
                    
                    Log::info("Generated default QR for Gudang {$gudang->kode_gudang}: " . strlen($qrCodeBase64) . ' chars');
                    
                    $qrDataArray[] = [
                        'gudang' => $gudang,
                        'qr_base64' => 'data:image/svg+xml;base64,' . $qrCodeBase64,
                        'qr_text' => $gudang->kode_gudang,
                        'has_qr' => strlen($qrCodeBase64) > 100
                    ];
                }
                
            } catch (\Exception $e) {
                Log::error("Error generating QR for Gudang {$gudang->kode_gudang}: " . $e->getMessage());
                Log::error("Stack trace: " . $e->getTraceAsString());
                
                // Fallback: No QR code
                $qrDataArray[] = [
                    'gudang' => $gudang,
                    'qr_base64' => null,
                    'qr_text' => $gudang->kode_gudang,
                    'has_qr' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        // Setup DOMPDF dengan konfigurasi yang lebih sederhana
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', realpath(base_path()));
        
        $dompdf = new Dompdf($options);

        // Generate HTML content
        $html = view('pdf.gudang-qr-codes', ['qrData' => collect($qrDataArray)])->render();
        
        // Debug: Save HTML to file for inspection
        file_put_contents(storage_path('app/debug_gudang_qr_pdf.html'), $html);
        Log::info('Gudang PDF HTML saved to: ' . storage_path('app/debug_gudang_qr_pdf.html'));
        
        // Load HTML to DOMPDF
        $dompdf->loadHtml($html);
        
        // Set paper size
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Return PDF download
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="gudang-qr-codes-' . date('Y-m-d') . '.pdf"');
    }
}
