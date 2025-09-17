<?php

namespace App\Jobs;

use App\Models\Tabung;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;

class GenerateQrCodesJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300; // 5 minutes timeout
    public $tries = 3;

    protected $downloadId;
    protected $tabungIds;
    protected $userId;
    protected $batchSize;

    /**
     * Create a new job instance.
     */
    public function __construct($downloadId, $tabungIds, $userId, $batchSize = 100)
    {
        $this->downloadId = $downloadId;
        $this->tabungIds = $tabungIds;
        $this->userId = $userId;
        $this->batchSize = $batchSize;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update status to processing
            DB::table('download_logs')->where('id', $this->downloadId)->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            // Process in batches
            $tabungs = Tabung::whereIn('id', $this->tabungIds)->get();
            $batches = $tabungs->chunk($this->batchSize);
            $totalBatches = $batches->count();
            $currentBatch = 0;

            // Create temporary directory
            $tempDir = storage_path("app/temp/qr_codes_{$this->downloadId}");
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            foreach ($batches as $batch) {
                $currentBatch++;
                
                // Generate QR codes for this batch
                foreach ($batch as $tabung) {
                    $qrCode = QrCode::format('png')
                        ->size(300)
                        ->margin(2)
                        ->generate($tabung->kode_tabung);
                    
                    $filename = "{$tabung->kode_tabung}.png";
                    file_put_contents("{$tempDir}/{$filename}", $qrCode);
                }

                // Update progress
                $progress = round(($currentBatch / $totalBatches) * 80); // 80% for generation
                DB::table('download_logs')->where('id', $this->downloadId)->update([
                    'progress' => $progress,
                    'message' => "Generating QR codes: batch {$currentBatch}/{$totalBatches}"
                ]);
            }

            // Create ZIP file
            $this->createZipFile($tempDir);

            // Cleanup temp directory
            $this->cleanupTempDirectory($tempDir);

            // Update status to completed
            DB::table('download_logs')->where('id', $this->downloadId)->update([
                'status' => 'completed',
                'progress' => 100,
                'completed_at' => now(),
                'message' => 'QR codes generated successfully'
            ]);

        } catch (\Exception $e) {
            // Update status to failed
            DB::table('download_logs')->where('id', $this->downloadId)->update([
                'status' => 'failed',
                'message' => 'Error: ' . $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    private function createZipFile($tempDir): void
    {
        $zipPath = "qr_codes/qr_codes_{$this->downloadId}.zip";
        $fullZipPath = storage_path("app/{$zipPath}");
        
        // Ensure directory exists
        $zipDir = dirname($fullZipPath);
        if (!file_exists($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($fullZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            
            // Add all PNG files to ZIP
            $files = glob("{$tempDir}/*.png");
            $totalFiles = count($files);
            $currentFile = 0;

            foreach ($files as $file) {
                $currentFile++;
                $filename = basename($file);
                $zip->addFile($file, $filename);

                // Update progress for zipping (80-100%)
                if ($currentFile % 10 === 0 || $currentFile === $totalFiles) {
                    $zipProgress = 80 + round(($currentFile / $totalFiles) * 20);
                    DB::table('download_logs')->where('id', $this->downloadId)->update([
                        'progress' => $zipProgress,
                        'message' => "Creating ZIP file: {$currentFile}/{$totalFiles} files"
                    ]);
                }
            }

            $zip->close();

            // Update download log with file path
            DB::table('download_logs')->where('id', $this->downloadId)->update([
                'file_path' => $zipPath,
                'file_size' => filesize($fullZipPath),
            ]);
        } else {
            throw new \Exception('Failed to create ZIP file');
        }
    }

    private function cleanupTempDirectory($tempDir): void
    {
        if (file_exists($tempDir)) {
            $files = glob("{$tempDir}/*");
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($tempDir);
        }
    }

    public function failed(\Throwable $exception): void
    {
        DB::table('download_logs')->where('id', $this->downloadId)->update([
            'status' => 'failed',
            'message' => 'Job failed: ' . $exception->getMessage(),
            'completed_at' => now(),
        ]);
    }
}
