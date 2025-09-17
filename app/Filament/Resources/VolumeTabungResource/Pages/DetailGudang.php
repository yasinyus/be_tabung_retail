<?php

namespace App\Filament\Resources\VolumeTabungResource\Pages;

use App\Filament\Resources\VolumeTabungResource;
use App\Models\Gudang;
use App\Models\StokTabung;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class DetailGudang extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = VolumeTabungResource::class;

    public $gudang;
    public $gudangData;

    public function mount($gudang): void
    {
        $this->gudang = $gudang;
        $this->gudangData = Gudang::where('kode_gudang', $gudang)->first();
        
        if (!$this->gudangData) {
            abort(404, 'Gudang tidak ditemukan');
        }
    }

    public function getTitle(): string
    {
        return "Detail Tabung - {$this->gudangData->nama_gudang}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StokTabung::query()
                    ->where('lokasi', $this->gudang)
                    ->with(['tabung'])
            )
            ->columns([
                TextColumn::make('kode_tabung')
                    ->label('Kode Tabung')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tabung.seri_tabung')
                    ->label('Seri Tabung')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Isi' => 'success',
                        'Kosong' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('volume')
                    ->label('Volume')
                    ->suffix(' L')
                    ->alignEnd(),
                TextColumn::make('tanggal_update')
                    ->label('Terakhir Update')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('kode_tabung')
            ->paginated([10, 25, 50])
            ->heading("Daftar Tabung di {$this->gudangData->nama_gudang}")
            ->description(function () {
                $total = StokTabung::where('lokasi', $this->gudang)->count();
                $isi = StokTabung::where('lokasi', $this->gudang)->where('status', 'Isi')->count();
                $kosong = StokTabung::where('lokasi', $this->gudang)->where('status', 'Kosong')->count();
                
                return "Total: {$total} tabung | Isi: {$isi} | Kosong: {$kosong}";
            });
    }
}
