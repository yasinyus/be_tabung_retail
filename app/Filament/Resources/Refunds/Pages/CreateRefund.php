<?php

namespace App\Filament\Resources\Refunds\Pages;

use App\Filament\Resources\Refunds\RefundResource;
use App\Models\SerahTerimaTabung;
use App\Models\SaldoPelanggan;
use App\Models\LaporanPelanggan;
use App\Models\Pelanggan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
            // Cari data serah terima dan pelanggan
            $serahTerima = SerahTerimaTabung::where('bast_id', $bast_id)->first();
            
            $formData = ['bast_id' => $bast_id];
            
            if ($serahTerima && $serahTerima->kode_pelanggan) {
                $pelanggan = Pelanggan::where('kode_pelanggan', $serahTerima->kode_pelanggan)->first();
                
                if ($pelanggan) {
                    $formData['kode_pelanggan'] = $pelanggan->kode_pelanggan;
                    $formData['harga_per_m3'] = $pelanggan->harga_tabung;
                    
                    Log::info('CreateRefund mount - Pelanggan found', [
                        'kode' => $pelanggan->kode_pelanggan,
                        'harga' => $pelanggan->harga_tabung
                    ]);
                }
            }
            
            // Fill form dengan data yang sudah disiapkan
            $this->form->fill($formData);
            
            Log::info('CreateRefund mount - form filled', $formData);
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
                
                // Hitung jumlah tabung dari list_tabung JSON
                $jumlahTabung = 0;
                if ($serahTerima->tabung && is_array($serahTerima->tabung)) {
                    $jumlahTabung = count($serahTerima->tabung);
                } elseif ($serahTerima->tabung && is_string($serahTerima->tabung)) {
                    $tabungArray = json_decode($serahTerima->tabung, true);
                    $jumlahTabung = $tabungArray ? count($tabungArray) : 0;
                }
                
                // Insert ke tabel laporan_pelanggan
                try {
                    LaporanPelanggan::create([
                        'tanggal' => Carbon::now(),
                        'kode_pelanggan' => $serahTerima->kode_pelanggan,
                        'keterangan' => 'Refund',
                        'id_bast_invoice' => $record->bast_id,
                        'list_tabung' => $serahTerima->tabung ?? [], // Ambil dari serah_terima_tabungs
                        'tabung' => $jumlahTabung, // Hitung jumlah dari list_tabung
                        'harga' => $record->total_refund,
                        'tambahan_deposit' => $record->total_refund, // tambah_deposit = total refund
                        'pengurangan_deposit' => 0,
                        'sisa_deposit' => $saldoPelanggan->saldo, // Saldo setelah ditambah refund
                        'konfirmasi' => 0,
                    ]);
                    
                    Log::info("Laporan pelanggan created for refund - Pelanggan: {$serahTerima->kode_pelanggan}, Total: {$record->total_refund}");
                } catch (\Exception $e) {
                    Log::error("Failed to create laporan_pelanggan: " . $e->getMessage());
                }
                
                // Tampilkan notifikasi sukses
                Notification::make()
                    ->title('Refund berhasil dibuat')
                    ->body("Saldo pelanggan {$serahTerima->kode_pelanggan} telah ditambah sebesar " . 
                           'Rp ' . number_format($record->total_refund, 0, ',', '.') . 
                           ' dan laporan pelanggan telah diperbarui.')
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
