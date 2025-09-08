<?php

namespace App\Filament\Resources\Audits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Gudang;
use App\Models\Pelanggan;
use App\Models\Tabung;
use App\Models\Audit;
use App\Models\VolumeTabung;
use App\Models\TabungActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                // Override query to show all tabung instead of audits
                return Tabung::query();
            })
            ->columns([
                TextColumn::make('kode_tabung')
                    ->label('Kode Tabung')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_audit')
                    ->label('Tanggal Audit')
                    ->getStateUsing(function ($record) {
                        // Find the latest audit that contains this tabung's qr_code
                        $latestAudit = Audit::whereRaw(
                            "JSON_SEARCH(tabung, 'one', ?, null, '$[*].qr_code') IS NOT NULL",
                            [$record->kode_tabung]
                        )
                        ->orderBy('tanggal', 'desc')
                        ->first();
                        
                        return $latestAudit ? Carbon::parse($latestAudit->tanggal)->format('d/m/Y') : '-';
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status_tabung')
                    ->label('Status Tabung')
                    ->getStateUsing(function ($record) {
                        // Find the latest audit that contains this tabung
                        $latestAudit = Audit::whereRaw(
                            "JSON_SEARCH(tabung, 'one', ?, null, '$[*].qr_code') IS NOT NULL",
                            [$record->kode_tabung]
                        )
                        ->orderBy('tanggal', 'desc')
                        ->first();
                        
                        if (!$latestAudit) {
                            return 'Belum pernah diaudit';
                        }
                        
                        // Calculate days from audit date to today
                        $tanggalAudit = Carbon::parse($latestAudit->tanggal);
                        $today = Carbon::today();
                        $daysDiff = $tanggalAudit->diffInDays($today);
                        
                        if ($daysDiff == 0) {
                            return 'Tidak aktif';
                        } else {
                            return $daysDiff . ' hari lalu';
                        }
                    })
                    ->sortable(),

                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->getStateUsing(function ($record) {
                        // Get location from the audit that contains this tabung
                        $latestAudit = Audit::whereRaw(
                            "JSON_SEARCH(tabung, 'one', ?, null, '$[*].qr_code') IS NOT NULL",
                            [$record->kode_tabung]
                        )
                        ->orderBy('tanggal', 'desc')
                        ->first();
                        
                        if (!$latestAudit) {
                            return 'Belum pernah diaudit';
                        }
                        
                        $lokasi = $latestAudit->lokasi;
                        
                        // Check if it's a gudang code (starts with GD)
                        if (str_starts_with($lokasi, 'GD')) {
                            $gudang = Gudang::where('kode_gudang', $lokasi)->first();
                            return $gudang ? $gudang->nama_gudang : $lokasi;
                        }
                        
                        // Check if it's a pelanggan code (starts with PA or PU)
                        if (str_starts_with($lokasi, 'PA') || str_starts_with($lokasi, 'PU')) {
                            $pelanggan = Pelanggan::where('kode_pelanggan', $lokasi)->first();
                            return $pelanggan ? $pelanggan->nama_pelanggan : $lokasi;
                        }
                        
                        // Return original location if no match found
                        return $lokasi;
                    })
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('lokasi')
                    ->label('Lokasi')
                    ->options(function () {
                        $lokasi = [];
                        
                        // Get unique locations from audits
                        $auditLokasi = Audit::distinct('lokasi')->whereNotNull('lokasi')->pluck('lokasi');
                        
                        foreach ($auditLokasi as $lok) {
                            if (str_starts_with($lok, 'GD')) {
                                $gudang = Gudang::where('kode_gudang', $lok)->first();
                                $lokasi[$lok] = $gudang ? $gudang->nama_gudang : $lok;
                            } elseif (str_starts_with($lok, 'PA') || str_starts_with($lok, 'PU')) {
                                $pelanggan = Pelanggan::where('kode_pelanggan', $lok)->first();
                                $lokasi[$lok] = $pelanggan ? $pelanggan->nama_pelanggan : $lok;
                            } else {
                                $lokasi[$lok] = $lok;
                            }
                        }
                        
                        return $lokasi;
                    }),
                    
                SelectFilter::make('status_audit')
                    ->label('Status Audit')
                    ->options([
                        'sudah_diaudit' => 'Sudah Diaudit',
                        'belum_diaudit' => 'Belum Diaudit',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'sudah_diaudit') {
                            return $query->whereExists(function ($subquery) {
                                $subquery->select(DB::raw(1))
                                    ->from('audits')
                                    ->whereRaw("JSON_SEARCH(audits.tabung, 'one', tabungs.kode_tabung, null, '$[*].qr_code') IS NOT NULL");
                            });
                        } elseif ($data['value'] === 'belum_diaudit') {
                            return $query->whereNotExists(function ($subquery) {
                                $subquery->select(DB::raw(1))
                                    ->from('audits')
                                    ->whereRaw("JSON_SEARCH(audits.tabung, 'one', tabungs.kode_tabung, null, '$[*].qr_code') IS NOT NULL");
                            });
                        }
                        
                        return $query;
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('History Data Tabung')
                    ->modalContent(function ($record) {
                        // Get all audit history for this tabung
                        $tabungHistory = Audit::whereRaw(
                            "JSON_SEARCH(tabung, 'one', ?, null, '$[*].qr_code') IS NOT NULL",
                            [$record->kode_tabung]
                        )
                        ->orderBy('tanggal', 'desc')
                        ->get();
                        
                        return view('filament.components.tabung-history', [
                            'kodeTabung' => $record->kode_tabung,
                            'history' => $tabungHistory
                        ]);
                    })
                    ->modalWidth('7xl'),
                    
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(function ($record) {
                        // Find the audit record that contains this tabung
                        $auditRecord = Audit::whereRaw(
                            "JSON_SEARCH(tabung, 'one', ?, null, '$[*].qr_code') IS NOT NULL",
                            [$record->kode_tabung]
                        )
                        ->orderBy('tanggal', 'desc')
                        ->first();
                        
                        if ($auditRecord) {
                            return route('filament.admin.resources.audits.edit', ['record' => $auditRecord->id]);
                        }
                        
                        return null;
                    })
                    ->visible(function ($record) {
                        // Only show edit if there's an audit record for this tabung
                        return Audit::whereRaw(
                            "JSON_SEARCH(tabung, 'one', ?, null, '$[*].qr_code') IS NOT NULL",
                            [$record->kode_tabung]
                        )->exists();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('kode_tabung', 'asc');
    }
}
