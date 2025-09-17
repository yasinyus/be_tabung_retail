<?php

namespace App\Filament\Resources\Tabungs\Pages;

use App\Filament\Resources\Tabungs\TabungResource;
use App\Models\DownloadLog;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ProgressColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadProgress extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = TabungResource::class;

    protected static ?string $title = 'Monitor Download QR Codes';

    public function getView(): string
    {
        return 'filament.resources.tabungs.pages.download-progress';
    }

    public function table(Table $table): Table
    {
        $userId = Auth::id();
        if (!$userId) {
            return $table->query(DownloadLog::whereRaw('1 = 0')); // Empty query
        }

        return $table
            ->query(
                DownloadLog::query()
                    ->where('user_id', $userId)
                    ->where('type', 'qr_codes')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Dimulai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                TextColumn::make('message')
                    ->label('Status')
                    ->wrap()
                    ->limit(50),
                    
                TextColumn::make('status')
                    ->label('Progress')
                    ->badge()
                    ->color(fn ($record) => $record->status_badge_color),
                    
                TextColumn::make('progress')
                    ->label('Kemajuan')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->alignCenter(),
                    
                TextColumn::make('file_size_human')
                    ->label('Ukuran File')
                    ->alignCenter(),
                    
                TextColumn::make('completed_at')
                    ->label('Selesai')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('5s') // Auto refresh every 5 seconds
            ->emptyStateHeading('Belum ada download')
            ->emptyStateDescription('Belum ada proses download QR codes yang dimulai.');
    }
}
