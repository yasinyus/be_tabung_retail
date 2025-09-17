<?php

namespace App\Filament\Resources\VolumeTabungResource\Pages;

use App\Filament\Resources\VolumeTabungResource;
use App\Models\Gudang;
use App\Models\StokTabung;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListGudang extends ListRecords implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = VolumeTabungResource::class;

    protected static ?string $title = 'Daftar Gudang & Statistik Tabung';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Gudang::query()
                    ->select([
                        'gudangs.*',
                        DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = gudangs.kode_gudang) as total_tabung'),
                        DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = gudangs.kode_gudang AND status = "Isi") as tabung_isi'),
                        DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = gudangs.kode_gudang AND status = "Kosong") as tabung_kosong')
                    ])
            )
            ->columns([
                TextColumn::make('kode_gudang')
                    ->label('Kode Gudang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_gudang')
                    ->label('Nama Gudang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_tabung')
                    ->label('Total Tabung')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('tabung_isi')
                    ->label('Tabung Isi')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),
                TextColumn::make('tabung_kosong')
                    ->label('Tabung Kosong')
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),
                TextColumn::make('detail')
                    ->label('Action')
                    ->formatStateUsing(fn ($record) => 'Lihat Detail')
                    ->url(fn ($record): string => route('filament.admin.resources.volume-tabungs.gudang-detail', ['gudang' => $record->kode_gudang]))
                    ->color('primary')
                    ->weight('bold'),
            ])
            ->defaultSort('nama_gudang')
            ->paginated([10, 25, 50]);
    }
}
