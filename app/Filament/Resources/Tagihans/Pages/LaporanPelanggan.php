<?php

namespace App\Filament\Resources\Tagihans\Pages;

use App\Filament\Resources\Tagihans\TagihanResource;
use App\Models\LaporanPelanggan as LaporanPelangganModel;
use App\Models\Pelanggan;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions;
use Illuminate\Http\Request;
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
            Actions\Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn () => route('laporan-pelanggan.export.pdf', ['kode_pelanggan' => $this->kodePelanggan]))
                ->openUrlInNewTab(),
                
            Actions\Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('info')
                ->url(fn () => route('laporan-pelanggan.export.excel', ['kode_pelanggan' => $this->kodePelanggan]))
                ->openUrlInNewTab(),
                
            Actions\Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(route('filament.admin.resources.tagihans.index')),
        ];
    }
}
