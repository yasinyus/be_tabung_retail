<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

class PelangganQrCodePdfController extends Controller
{
    public function downloadPelangganQrCodes()
    {
        // Ambil semua data pelanggan
        $pelanggans = Pelanggan::all();
        
        if ($pelanggans->isEmpty()) {
            return response('No pelanggan data found', 404);
        }

        // Generate QR codes untuk setiap pelanggan
        $qrDataArray = [];
        foreach ($pelanggans as $pelanggan) {
            try {
                // Generate QR code langsung untuk pelanggan
                $qrContent = json_encode([
                    'id' => $pelanggan->id,
                    'code' => $pelanggan->kode_pelanggan,
                    'url' => url("/pelanggan/{$pelanggan->id}")
                ]);

                Log::info("Generating QR for Pelanggan {$pelanggan->kode_pelanggan} with content: " . $qrContent);

                // Coba SVG dulu, jika gagal pakai fallback
                try {
                    $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(150)
                        ->margin(1)
                        ->generate($qrContent);
                    
                    $qrCodeBase64 = base64_encode($qrCodeSvg);
                    
                    Log::info("Generated QR SVG for Pelanggan {$pelanggan->kode_pelanggan}: " . strlen($qrCodeBase64) . ' chars');
                    
                    $qrDataArray[] = [
                        'pelanggan' => $pelanggan,
                        'qr_base64' => 'data:image/svg+xml;base64,' . $qrCodeBase64,
                        'qr_text' => $pelanggan->kode_pelanggan,
                        'has_qr' => strlen($qrCodeBase64) > 100
                    ];
                } catch (\Exception $svgError) {
                    Log::warning("SVG QR failed for Pelanggan {$pelanggan->kode_pelanggan}, trying default format: " . $svgError->getMessage());
                    
                    // Fallback ke format default
                    $qrCodeDefault = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
                        ->margin(1)
                        ->generate($qrContent);
                    
                    $qrCodeBase64 = base64_encode($qrCodeDefault);
                    
                    Log::info("Generated default QR for Pelanggan {$pelanggan->kode_pelanggan}: " . strlen($qrCodeBase64) . ' chars');
                    
                    $qrDataArray[] = [
                        'pelanggan' => $pelanggan,
                        'qr_base64' => 'data:image/svg+xml;base64,' . $qrCodeBase64,
                        'qr_text' => $pelanggan->kode_pelanggan,
                        'has_qr' => strlen($qrCodeBase64) > 100
                    ];
                }
                
            } catch (\Exception $e) {
                Log::error("Error generating QR for Pelanggan {$pelanggan->kode_pelanggan}: " . $e->getMessage());
                Log::error("Stack trace: " . $e->getTraceAsString());
                
                // Fallback: No QR code
                $qrDataArray[] = [
                    'pelanggan' => $pelanggan,
                    'qr_base64' => null,
                    'qr_text' => $pelanggan->kode_pelanggan,
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
        $html = view('pdf.pelanggan-qr-codes', ['qrData' => collect($qrDataArray)])->render();
        
        // Debug: Save HTML to file for inspection
        file_put_contents(storage_path('app/debug_pelanggan_qr_pdf.html'), $html);
        Log::info('Pelanggan PDF HTML saved to: ' . storage_path('app/debug_pelanggan_qr_pdf.html'));
        
        // Load HTML to DOMPDF
        $dompdf->loadHtml($html);
        
        // Set paper size
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Return PDF download
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="pelanggan-qr-codes-' . date('Y-m-d') . '.pdf"');
    }
}
