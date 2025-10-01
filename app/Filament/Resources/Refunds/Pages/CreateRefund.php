<?php

namespace App\Filament\Resources\Refunds\Pages;

use App\Filament\Resources\Refunds\RefundResource;
use App\Models\SerahTerimaTabung;
use App\Models\SaldoPelanggan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateRefund extends CreateRecord
{
    protected static string $resource = RefundResource::class;

    public function getTitle(): string
    {
        return 'Buat Refund Baru';
    }

    public function mount(): void
    {
        parent::mount();
        
        // Pre-fill form jika ada parameter dari URL
        $bast_id = request()->get('bast_id');
        
        // Debug: log untuk memastikan parameter dibaca
        Log::info('CreateRefund mount - bast_id from URL: ' . $bast_id);
        
        if ($bast_id) {
            // Set data langsung ke property form
            $this->data['bast_id'] = $bast_id;
            
            // Alternative: juga coba fill form
            $this->form->fill([
                'bast_id' => $bast_id,
            ]);
            
            Log::info('CreateRefund mount - form filled with bast_id: ' . $bast_id);
        }
    }

    protected function getFormData(): array
    {
        $data = parent::getFormData();
        
        // Pre-fill form jika ada parameter dari URL
        $bast_id = request()->get('bast_id');
        
        if ($bast_id && empty($data['bast_id'])) {
            $data['bast_id'] = $bast_id;
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Jika ada parameter bast_id dari URL, pastikan digunakan
        $bast_id = request()->get('bast_id');
        
        if ($bast_id && empty($data['bast_id'])) {
            $data['bast_id'] = $bast_id;
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        
        // Jika ada bast_id, cari kode_pelanggan dari serah_terima_tabungs
        if ($record->bast_id) {
            $serahTerima = SerahTerimaTabung::where('bast_id', $record->bast_id)->first();
            
            if ($serahTerima && $serahTerima->kode_pelanggan && $record->total_refund > 0) {
                // Cari atau buat record saldo pelanggan
                $saldoPelanggan = SaldoPelanggan::firstOrCreate(
                    ['kode_pelanggan' => $serahTerima->kode_pelanggan],
                    ['saldo' => 0]
                );
                
                // Tambah saldo dengan nilai refund
                $saldoPelanggan->saldo += $record->total_refund;
                $saldoPelanggan->save();
                
                Log::info("Saldo ditambahkan untuk pelanggan {$serahTerima->kode_pelanggan}: +{$record->total_refund}");
                
                // Tampilkan notifikasi sukses
                Notification::make()
                    ->title('Refund berhasil dibuat')
                    ->body("Saldo pelanggan {$serahTerima->kode_pelanggan} telah ditambah sebesar " . 
                           'Rp ' . number_format($record->total_refund, 0, ',', '.'))
                    ->success()
                    ->send();
            } else {
                // Jika tidak ditemukan serah terima atau data tidak lengkap
                Notification::make()
                    ->title('Perhatian')
                    ->body('Refund dibuat tetapi saldo pelanggan tidak dapat diperbarui karena data BAST tidak ditemukan atau tidak lengkap.')
                    ->warning()
                    ->send();
            }
        }
    }
}
