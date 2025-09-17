<?php

namespace App\Filament\Resources\Tabungs\Pages;

use App\Filament\Resources\Tabungs\TabungResource;
use App\Models\Tabung;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Log;

class ListTabungs extends ListRecords
{
    protected static string $resource = TabungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Create Tabung')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(TabungResource::getUrl('create')),
            
            Action::make('downloadQrCodes')
                ->label('Download QR Codes')
                ->icon('heroicon-o-qr-code')
                ->color('success')
                ->form([
                    TextInput::make('jumlah_tabung')
                        ->label('Jumlah Tabung')
                        ->numeric()
                        ->default(100)
                        ->required()
                        ->minValue(1)
                        ->maxValue(1000)
                        ->helperText('Masukkan jumlah tabung yang ingin didownload (max: 1000)'),
                    
                    Select::make('range_type')
                        ->label('Pilih Range Data')
                        ->options([
                            'latest' => 'Tabung Terbaru (ID terbesar)',
                            'oldest' => 'Tabung Terlama (ID terkecil)',
                            'custom' => 'Range Custom (ID awal - ID akhir)',
                            'random' => 'Random Tabung',
                        ])
                        ->default('latest')
                        ->required()
                        ->live()
                        ->helperText('Pilih metode pengambilan data'),
                    
                    TextInput::make('start_id')
                        ->label('ID Awal')
                        ->numeric()
                        ->minValue(1)
                        ->visible(fn ($get) => $get('range_type') === 'custom')
                        ->required(fn ($get) => $get('range_type') === 'custom')
                        ->helperText('ID tabung mulai dari'),
                    
                    TextInput::make('end_id')
                        ->label('ID Akhir')
                        ->numeric()
                        ->minValue(1)
                        ->visible(fn ($get) => $get('range_type') === 'custom')
                        ->required(fn ($get) => $get('range_type') === 'custom')
                        ->helperText('ID tabung sampai dengan'),
                ])
                ->action(function (array $data) {
                    try {
                        $result = $this->downloadQrCodesSimple($data);
                        
                        // Create download URL from file name
                        if (is_array($result) && isset($result['file'])) {
                            $downloadUrl = url("/download/temp/{$result['file']}");
                            
                            // Show success notification with download info
                            Notification::make()
                                ->success()
                                ->title('PDF berhasil dihasilkan!')
                                ->body($result['message'] ?? 'File PDF siap didownload')
                                ->send();
                            
                            // Use JavaScript to open download URL
                            $this->js("window.open('{$downloadUrl}', '_blank')");
                        }
                        
                        return $result;
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Error Download')
                            ->body($e->getMessage())
                            ->send();
                        return null;
                    }
                }),
        ];
    }
    
    public function downloadQrCodesSimple(array $data)
    {
        try {
            // Set time limit dan memory untuk mencegah timeout
            set_time_limit(300); // 5 menit
            ini_set('memory_limit', '512M');
            
            // Ambil parameter dari form
            $jumlahTabung = (int) $data['jumlah_tabung'];
            $rangeType = $data['range_type'];
            
            // Build query berdasarkan range type
            $query = Tabung::query();
            
            switch ($rangeType) {
                case 'latest':
                    $query->orderByDesc('id')->limit($jumlahTabung);
                    $description = "tabung terbaru";
                    break;
                    
                case 'oldest':
                    $query->orderBy('id')->limit($jumlahTabung);
                    $description = "tabung terlama";
                    break;
                    
                case 'custom':
                    $startId = (int) $data['start_id'];
                    $endId = (int) $data['end_id'];
                    
                    if ($startId > $endId) {
                        throw new \InvalidArgumentException('ID awal harus lebih kecil dari ID akhir.');
                    }
                    
                    $query->whereBetween('id', [$startId, $endId])->limit($jumlahTabung);
                    $description = "tabung ID {$startId} - {$endId}";
                    break;
                    
                case 'random':
                    $query->inRandomOrder()->limit($jumlahTabung);
                    $description = "tabung random";
                    break;
                    
                default:
                    $query->limit($jumlahTabung);
                    $description = "tabung";
            }
            
            $tabungs = $query->get();
            
            if ($tabungs->isEmpty()) {
                throw new \InvalidArgumentException('Tidak ada tabung yang sesuai dengan filter yang dipilih.');
            }

            // Generate QR codes untuk setiap tabung (EXACT copy dari TabungQrCodePdfController)
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
                        $qrCodeSvg = QrCode::format('svg')
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
                        $qrCodeDefault = QrCode::size(150)
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

            // Setup DOMPDF dengan konfigurasi yang EXACT sama seperti TabungQrCodePdfController
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', false); // Disable remote untuk keamanan
            $options->set('isHtml5ParserEnabled', true);
            $options->set('chroot', realpath(base_path()));
            
            $dompdf = new Dompdf($options);

            // Generate HTML content menggunakan view yang sama
            $html = view('pdf.tabung-qr-codes', ['qrData' => collect($qrDataArray)])->render();
            
            // Debug: Save HTML to file for inspection
            file_put_contents(storage_path('app/debug_tabung_qr_pdf_filtered.html'), $html);
            Log::info('Tabung PDF HTML saved to: ' . storage_path('app/debug_tabung_qr_pdf_filtered.html'));
            
            // Load HTML to DOMPDF
            $dompdf->loadHtml($html);
            
            // Set paper size
            $dompdf->setPaper('A4', 'portrait');
            
            // Render PDF
            $dompdf->render();
            
            $totalTabung = count($qrDataArray);
            $fileName = "tabung-qr-codes-{$rangeType}-{$totalTabung}-" . date('Y-m-d') . ".pdf";
            
            // Save PDF to temporary storage
            $pdfContent = $dompdf->output();
            $tempPath = storage_path("app/temp/{$fileName}");
            
            // Ensure temp directory exists
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }
            
            file_put_contents($tempPath, $pdfContent);
            
            Log::info("PDF saved to: {$tempPath}");
            
            // Always return array response for Filament (no direct PDF response)
            return [
                'status' => 'success', 
                'file' => $fileName, 
                'temp_path' => $tempPath,
                'message' => "PDF berhasil dibuat. File: {$fileName}"
            ];
                
        } catch (\Exception $e) {
            Log::error("Error in downloadQrCodesSimple: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            // Jangan kirim notification, langsung throw agar Filament handle
            throw $e;
        }
    }
}
