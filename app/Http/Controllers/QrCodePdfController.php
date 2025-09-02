<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Armada;
use Dompdf\Dompdf;
use Dompdf\Options;

class QrCodePdfController extends Controller
{
    public function downloadArmadaQrCodes()
    {
        // Ambil semua data armada
        $armadas = Armada::all();
        
        if ($armadas->isEmpty()) {
            return response('No armada data found', 404);
        }

        // Generate QR codes untuk setiap armada
        $qrDataArray = [];
        foreach ($armadas as $armada) {
            try {
                // Generate QR code langsung tanpa melalui model untuk menghindari masalah encoding
                $qrContent = json_encode([
                    'id' => $armada->id,
                    'nopol' => $armada->nopol,
                    'url' => url("/armada/{$armada->id}")
                ]);

                Log::info("Generating QR for {$armada->nopol} with content: " . $qrContent);

                // Coba SVG dulu, jika gagal pakai fallback
                try {
                    $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(150)
                        ->margin(1)
                        ->generate($qrContent);
                    
                    $qrCodeBase64 = base64_encode($qrCodeSvg);
                    
                    Log::info("Generated QR SVG for {$armada->nopol}: " . strlen($qrCodeBase64) . ' chars');
                    
                    $qrDataArray[] = [
                        'armada' => $armada,
                        'qr_base64' => 'data:image/svg+xml;base64,' . $qrCodeBase64,
                        'qr_text' => $armada->nopol,
                        'has_qr' => strlen($qrCodeBase64) > 100
                    ];
                } catch (\Exception $svgError) {
                    Log::warning("SVG QR failed for {$armada->nopol}, trying default format: " . $svgError->getMessage());
                    
                    // Fallback ke format default
                    $qrCodeDefault = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
                        ->margin(1)
                        ->generate($qrContent);
                    
                    $qrCodeBase64 = base64_encode($qrCodeDefault);
                    
                    Log::info("Generated default QR for {$armada->nopol}: " . strlen($qrCodeBase64) . ' chars');
                    
                    $qrDataArray[] = [
                        'armada' => $armada,
                        'qr_base64' => 'data:image/svg+xml;base64,' . $qrCodeBase64,
                        'qr_text' => $armada->nopol,
                        'has_qr' => strlen($qrCodeBase64) > 100
                    ];
                }
                
            } catch (\Exception $e) {
                Log::error("Error generating QR for {$armada->nopol}: " . $e->getMessage());
                Log::error("Stack trace: " . $e->getTraceAsString());
                
                // Fallback: No QR code
                $qrDataArray[] = [
                    'armada' => $armada,
                    'qr_base64' => null,
                    'qr_text' => $armada->nopol,
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
        $html = view('pdf.armada-qr-simple', ['qrData' => collect($qrDataArray)])->render();
        
        // Debug: Save HTML to file for inspection
        file_put_contents(storage_path('app/debug_qr_pdf.html'), $html);
        Log::info('PDF HTML saved to: ' . storage_path('app/debug_qr_pdf.html'));
        
        // Load HTML to DOMPDF
        $dompdf->loadHtml($html);
        
        // Set paper size
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Return PDF download
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="armada-qr-codes-' . date('Y-m-d') . '.pdf"');
    }
}
