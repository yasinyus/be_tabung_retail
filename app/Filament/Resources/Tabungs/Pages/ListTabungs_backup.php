<?php

namespace App\Filament\Resources\Tabungs\Pages;

use App\Filament\Resources\Tabungs\TabungResource;
use App\Jobs\GenerateQrCodesJob;
use App\Models\DownloadLog;
use App\Models\Tabung;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

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
                    Select::make('batch_size')
                        ->label('Jumlah Data')
                        ->options([
                            '100' => '100 tabung teratas',
                            '200' => '200 tabung teratas', 
                            '300' => '300 tabung teratas',
                            '500' => '500 tabung teratas',
                            'all' => 'Semua tabung (Berisiko timeout)',
                        ])
                        ->default('100')
                        ->required()
                        ->helperText('Pilih jumlah data untuk mempercepat proses'),
                ])
                ->action(function (array $data) {
                    return $this->downloadQrCodesSimple($data);
                }),
                
            Action::make('downloadProgress')
                ->label('Monitor Download')
                ->icon('heroicon-o-clock')
                ->color('info')
                ->url(route('filament.admin.resources.tabungs.download-progress'))
                ->visible(fn () => Auth::check() && DownloadLog::where('user_id', Auth::id())->where('status', '!=', 'completed')->exists()),
        ];
    }

    protected function processQrCodeDownload(array $data): void
    {
        try {
            // Get tabung IDs based on filter
            $query = Tabung::query();
            
            switch ($data['filter_type']) {
                case 'range':
                    if ($data['start_id'] && $data['end_id']) {
                        $query->whereBetween('id', [$data['start_id'], $data['end_id']]);
                    }
                    break;
                case 'search':
                    if ($data['search_term']) {
                        $searchTerm = $data['search_term'];
                        $query->where(function($q) use ($searchTerm) {
                            $q->where('kode_tabung', 'like', "%{$searchTerm}%")
                              ->orWhere('seri_tabung', 'like', "%{$searchTerm}%");
                        });
                    }
                    break;
                // 'all' doesn't need additional filtering
            }
        }
    }
    
    public function downloadQrCodesSimple(array $data)
    {
        try {
            // Set time limit untuk mencegah timeout
            set_time_limit(300); // 5 menit
            ini_set('memory_limit', '512M');
            
            // Ambil data berdasarkan batch size
            $batchSize = $data['batch_size'];
            
            if ($batchSize === 'all') {
                $tabungs = Tabung::all();
            } else {
                $limit = (int) $batchSize;
                $tabungs = Tabung::limit($limit)->get();
            }
            
            if ($tabungs->isEmpty()) {
                Notification::make()
                    ->warning()
                    ->title('Tidak ada data')
                    ->body('Tidak ada tabung untuk didownload.')
                    ->send();
                return;
            }
            
            // Buat folder temporary
            $tempDir = storage_path('app/temp-qr-' . time());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $totalTabung = $tabungs->count();
            $processed = 0;
            
            // Generate QR codes
            foreach ($tabungs as $tabung) {
                $qrCode = \QrCode::format('png')
                    ->size(300)
                    ->generate(route('tabung.show', $tabung->id));
                
                $fileName = "qr-tabung-{$tabung->kode_tabung}.png";
                $filePath = $tempDir . '/' . $fileName;
                file_put_contents($filePath, $qrCode);
                
                $processed++;
                
                // Update progress setiap 50 item untuk performance
                if ($processed % 50 === 0) {
                    // Optional: bisa ditampilkan progress jika diperlukan
                }
            }
            
            // Buat ZIP file
            $zipFileName = 'qr-codes-tabung-' . date('Y-m-d-H-i-s') . '.zip';
            $zipPath = storage_path('app/public/' . $zipFileName);
            
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                $files = glob($tempDir . '/*.png');
                foreach ($files as $file) {
                    $zip->addFile($file, basename($file));
                }
                $zip->close();
                
                // Hapus folder temporary
                array_map('unlink', glob($tempDir . '/*'));
                rmdir($tempDir);
                
                // Download file
                Notification::make()
                    ->success()
                    ->title('Download berhasil!')
                    ->body("QR codes untuk {$totalTabung} tabung berhasil didownload.")
                    ->send();
                
                return response()->download($zipPath)->deleteFileAfterSend();
                
            } else {
                throw new \Exception('Gagal membuat file ZIP');
            }
            
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error!')
                ->body('Gagal download: ' . $e->getMessage())
                ->send();
        }
    }
}
