<?php

namespace App\Filament\Resources\Tagihans\Pages;

use App\Filament\Resources\Tagihans\TagihanResource;
use App\Models\LaporanPelanggan as LaporanPelangganModel;
use App\Models\Pelanggan;
use App\Models\SaldoPelanggan;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPelanggan extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = TagihanResource::class;
    
    protected string $view = 'filament.resources.tagihans.pages.laporan-pelanggan';
    
    public $kodePelanggan;
    public $pelanggan;
    
    public function mount(Request $request)
    {
        $this->kodePelanggan = $request->query('kode_pelanggan');
        
        if ($this->kodePelanggan) {
            $this->pelanggan = Pelanggan::where('kode_pelanggan', $this->kodePelanggan)->first();
        }
        
        if (!$this->pelanggan) {
            abort(404, 'Pelanggan tidak ditemukan');
        }
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                LaporanPelangganModel::query()
                    ->where('kode_pelanggan', $this->kodePelanggan)
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('created_at', 'desc')
            )
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->placeholder('Pilih tanggal mulai'),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->placeholder('Pilih tanggal akhir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari_tanggal'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d/m/Y');
                        }
                        if ($data['sampai_tanggal'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->columns([
                TextColumn::make('row_number')
                    ->label('No')
                    ->rowIndex(),
                    
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable()
                    ->wrap(),
                    
                TextColumn::make('id_bast_invoice')
                    ->label('ID BAST Invoice')
                    ->searchable()
                    ->placeholder('-')
                    ->alignCenter(),
                    
                TextColumn::make('tabung')
                    ->label('Jumlah Tabung')
                    ->placeholder('-')
                    ->alignCenter(),
                    
                TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->alignEnd(),
                    
                TextColumn::make('tambahan_deposit')
                    ->label('Deposit +')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->color('success')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => $state ? '+' . number_format($state, 0, ',', '.') : '-'),
                    
                TextColumn::make('pengurangan_deposit')
                    ->label('Deposit -')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->color('danger')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => $state ? '-' . number_format($state, 0, ',', '.') : '-'),
                    
                TextColumn::make('sisa_deposit')
                    ->label('Sisa')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->alignEnd(),
                    
                CheckboxColumn::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->alignCenter()
                    ->updateStateUsing(function ($record, $state) {
                        $record->update(['konfirmasi' => $state]);
                        return $state;
                    }),
                    
                TextColumn::make('download_link')
                    ->label('Export')
                    ->alignCenter()
                    ->state(function ($record) {
                        return 'Download PDF';
                    })
                    ->badge()
                    ->color('info')
                    ->url(fn ($record) => route('laporan.download-invoice', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->actions([
                \Filament\Actions\Action::make('batalkan')
                    ->label('Batalkan Transaksi')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Transaksi')
                    ->modalDescription(fn ($record) => 
                        'Apakah Anda yakin ingin membatalkan transaksi ini? ' .
                        'Saldo sebesar Rp ' . number_format($record->pengurangan_deposit ?? 0, 0, ',', '.') . 
                        ' akan dikembalikan ke pelanggan dan record ini akan dihapus.'
                    )
                    ->modalSubmitActionLabel('Ya, Batalkan')
                    ->action(function ($record) {
                        try {
                            DB::beginTransaction();
                            
                            // Simpan data untuk update record sebelumnya
                            $kodePelanggan = $record->kode_pelanggan;
                            $hargaYangSama = $record->total;
                            $jumlahDikembalikan = $record->pengurangan_deposit ?? 0;
                            
                            // 1. Kembalikan saldo pelanggan
                            $saldoPelanggan = SaldoPelanggan::where('kode_pelanggan', $kodePelanggan)->first();
                            
                            if ($saldoPelanggan) {
                                // Jika ada pengurangan deposit, kembalikan ke saldo
                                if ($record->pengurangan_deposit > 0) {
                                    $saldoPelanggan->saldo += $record->pengurangan_deposit;
                                }
                                
                                // Jika ada tambahan deposit, kurangi dari saldo
                                if ($record->tambahan_deposit > 0) {
                                    $saldoPelanggan->saldo -= $record->tambahan_deposit;
                                }
                                
                                $saldoPelanggan->save();
                            }
                            
                            // 2. Update sisa_deposit di record sebelumnya yang memiliki harga sama (data duplikasi)
                            if ($jumlahDikembalikan > 0 && $hargaYangSama > 0) {
                                // Cari record sebelumnya dengan harga yang sama
                                $recordSebelumnya = LaporanPelangganModel::where('kode_pelanggan', $kodePelanggan)
                                    ->where('total', $hargaYangSama)
                                    ->where('id', '<', $record->id) // Record sebelumnya (ID lebih kecil)
                                    ->orderBy('id', 'desc') // Ambil yang terdekat
                                    ->first();
                                
                                if ($recordSebelumnya) {
                                    // Update sisa_deposit dengan menambahkan jumlah yang dikembalikan
                                    $recordSebelumnya->sisa_deposit = ($recordSebelumnya->sisa_deposit ?? 0) + $jumlahDikembalikan;
                                    $recordSebelumnya->save();
                                }
                            }
                            
                            // 3. Hapus record laporan yang dibatalkan
                            $record->delete();
                            
                            DB::commit();
                            
                            Notification::make()
                                ->title('Transaksi Berhasil Dibatalkan')
                                ->body('Saldo pelanggan telah dikembalikan dan record telah dihapus.')
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            DB::rollBack();
                            
                            Notification::make()
                                ->title('Gagal Membatalkan Transaksi')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn ($record) => !empty($record->id_bast_invoice)),
            ])
            ->striped()
            ->defaultPaginationPageOption(25)
            ->emptyStateHeading('Belum ada laporan')
            ->emptyStateDescription('Belum ada data laporan untuk pelanggan ini.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
    
    public function getTitle(): string
    {
        return 'Laporan Pelanggan - ' . ($this->pelanggan->nama_pelanggan ?? '');
    }
    
    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(function () {
                    $filters = $this->tableFilters['tanggal'] ?? [];
                    return route('laporan-pelanggan.export.pdf', [
                        'kode_pelanggan' => $this->kodePelanggan,
                        'dari_tanggal' => $filters['dari_tanggal'] ?? null,
                        'sampai_tanggal' => $filters['sampai_tanggal'] ?? null,
                    ]);
                })
                ->openUrlInNewTab(),
                
            \Filament\Actions\Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('info')
                ->url(function () {
                    $filters = $this->tableFilters['tanggal'] ?? [];
                    return route('laporan-pelanggan.export.excel', [
                        'kode_pelanggan' => $this->kodePelanggan,
                        'dari_tanggal' => $filters['dari_tanggal'] ?? null,
                        'sampai_tanggal' => $filters['sampai_tanggal'] ?? null,
                    ]);
                })
                ->openUrlInNewTab(),
                
            \Filament\Actions\Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(route('filament.admin.resources.tagihans.index')),
        ];
    }
}
