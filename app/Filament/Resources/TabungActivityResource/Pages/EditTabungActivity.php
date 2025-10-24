<?php

namespace App\Filament\Resources\TabungActivityResource\Pages;

use App\Filament\Resources\TabungActivityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

use Illuminate\Support\Facades\Auth;

class EditTabungActivity extends EditRecord
{

    protected function authorizeAccess(): void
    {
        $user = Auth::user();
        if (!$user || ($user->role ?? null) !== 'admin_utama') {
            abort(403, 'Hanya admin_utama yang dapat mengedit aktivitas tabung.');
        }
    }

    public function mount($record): void
    {
        $this->authorizeAccess();
        parent::mount($record);
    }
    protected static string $resource = TabungActivityResource::class;

    public function getTitle(): string
    {
        return 'Edit Aktivitas Tabung';
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        // Hanya update jika aktivitas yang relevan
        $aktivitasYangMenampilkan = [
            'Kirim Tabung Meter',
            'Kirim Tabung Ke Agen',
            'Kirim Tabung Ke Pelanggan',
        ];
        if (!in_array($record->nama_aktivitas, $aktivitasYangMenampilkan)) {
            return;
        }


        // Hitung ulang total volume dan harga
        $tabungList = is_array($record->tabung) ? $record->tabung : [];
        $totalVolume = 0;
        foreach ($tabungList as $kodeTabung) {
            $stokTabung = \App\Models\StokTabung::where('kode_tabung', $kodeTabung)->first();
            if ($stokTabung) {
                $totalVolume += $stokTabung->volume ?? 0;
            }
        }

        // Ambil harga per m3 dari pelanggan tujuan
        $hargaPerM3 = 0;
        if ($record->tujuan) {
            $pelanggan = \App\Models\Pelanggan::where('kode_pelanggan', $record->tujuan)
                ->orWhere('nama_pelanggan', $record->tujuan)
                ->first();
            if ($pelanggan && $pelanggan->harga_tabung) {
                $hargaPerM3 = $pelanggan->harga_tabung;
            }
        }
        $totalHarga = $hargaPerM3 * $totalVolume;

        // Update juga tabel aktivitas_tabung (record ini)
    $record->total_tabung = count($tabungList);
    $record->save();

        // Update detail_transaksi dan transactions yang terkait (berdasarkan trx_id = id_bast_invoice)
        $laporan = \App\Models\LaporanPelanggan::where('id_bast_invoice', $record->id)->first();
        if ($laporan) {
            $trx_id = $laporan->id_bast_invoice;
            $detail = \App\Models\DetailTransaksi::where('trx_id', $trx_id)->first();
            if ($detail) {
                // Buat array tabung: [{kode_tabung, volume}]
                $tabungArr = collect($tabungList)->map(function($kode) {
                    $stok = \App\Models\StokTabung::where('kode_tabung', $kode)->first();
                    return [
                        'kode_tabung' => $kode,
                        'volume' => $stok ? (float) $stok->volume : 0
                    ];
                })->toArray();
                $detail->tabung = $tabungArr;
                $detail->keterangan = 'Volume total: ' . collect($tabungArr)->sum('volume') . ' m³';
                $detail->save();
            }

            // Update transactions.total
            $transaction = \App\Models\Transaction::where('trx_id', $trx_id)->first();
            if ($transaction) {
                $transaction->total = $totalHarga;
                $transaction->save();
            }

            // Update laporan_pelanggan
            $laporan->harga = $totalHarga;
            $laporan->save();

            // Update saldo pelanggan jika harga berubah
            $saldoPelanggan = \App\Models\SaldoPelanggan::where('kode_pelanggan', $laporan->kode_pelanggan)->first();
            if ($saldoPelanggan) {
                // Hitung selisih harga lama dan baru
                $selisih = ($laporan->pengurangan_deposit ?? 0) - $totalHarga;
                $saldoPelanggan->saldo += $selisih;
                $saldoPelanggan->save();
                $laporan->pengurangan_deposit = $totalHarga;
                $laporan->sisa_deposit = $saldoPelanggan->saldo;
                $laporan->save();
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat'),
            DeleteAction::make()
                ->label('Hapus'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert tabung array back to repeater format
        if (isset($data['tabung']) && is_array($data['tabung'])) {
            $tabungItems = [];
            foreach ($data['tabung'] as $qrCode) {
                // Data tabung berupa array string, convert ke format repeater
                $tabungItems[] = ['qr_code' => $qrCode];
            }
            $data['tabung'] = $tabungItems;
        } elseif (empty($data['tabung'])) {
            // Jika tidak ada data tabung, set array kosong
            $data['tabung'] = [];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert tabung repeater back to array format
        if (isset($data['tabung']) && is_array($data['tabung'])) {
            $tabungCodes = [];
            foreach ($data['tabung'] as $item) {
                if (isset($item['qr_code']) && !empty($item['qr_code'])) {
                    $tabungCodes[] = $item['qr_code'];
                }
            }
            $data['tabung'] = $tabungCodes;
        }

        // Update total_tabung based on tabung count
        $data['total_tabung'] = count($data['tabung'] ?? []);

        return $data;
    }
}
