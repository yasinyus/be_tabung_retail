<?php

namespace App\Filament\Resources\TabungActivityResource\Tables;

use App\Models\TabungActivity;
use App\Models\Tabung;
use App\Models\Gudang;
use App\Models\Pelanggan;
use App\Models\Armada;
use App\Filament\Resources\TabungActivityResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class TabungActivitiesTable
{
    /**
     * Get details for a code (T-001, GDG-001, PLG-001, or license plate)
     */
    protected static function getCodeDetails(string $code): ?array
    {
        $code = trim($code);
        
        // Check for Tabung (T-001)
        if (preg_match('/^T-\d+$/', $code)) {
            $tabung = Tabung::where('kode_tabung', $code)->first();
            if ($tabung) {
                return [
                    'type' => 'Tabung',
                    'title' => "Detail Tabung - {$tabung->kode_tabung}",
                    'details' => [
                        'Kode Tabung' => $tabung->kode_tabung,
                        'Seri Tabung' => $tabung->seri_tabung ?? 'Tidak ada',
                        'Tahun' => $tabung->tahun ?? 'Tidak ada',
                        'Keterangan' => $tabung->keterangan ?? 'Tidak ada keterangan',
                    ]
                ];
            }
        }
        
        // Check for Gudang (GDG-001)
        if (preg_match('/^GDG-\d+$/', $code)) {
            $gudang = Gudang::where('kode_gudang', $code)->first();
            if ($gudang) {
                return [
                    'type' => 'Gudang',
                    'title' => "Detail Gudang - {$gudang->kode_gudang}",
                    'details' => [
                        'Kode Gudang' => $gudang->kode_gudang,
                        'Nama Gudang' => $gudang->nama_gudang ?? 'Tidak ada',
                        'Lokasi' => $gudang->lokasi_gudang ?? 'Tidak ada',
                        'Penanggung Jawab' => $gudang->penanggung_jawab ?? 'Tidak ada',
                    ]
                ];
            }
        }
        
        // Check for Pelanggan (PLG-001)
        if (preg_match('/^PLG-\d+$/', $code)) {
            $pelanggan = Pelanggan::where('kode_pelanggan', $code)->first();
            if ($pelanggan) {
                return [
                    'type' => 'Pelanggan',
                    'title' => "Detail Pelanggan - {$pelanggan->kode_pelanggan}",
                    'details' => [
                        'Kode Pelanggan' => $pelanggan->kode_pelanggan,
                        'Nama Pelanggan' => $pelanggan->nama_pelanggan ?? 'Tidak ada',
                        'Email' => $pelanggan->email ?? 'Tidak ada',
                        'Jenis Pelanggan' => ucfirst($pelanggan->jenis_pelanggan ?? 'Tidak ada'),
                        'Lokasi' => $pelanggan->lokasi_pelanggan ?? 'Tidak ada',
                        'Harga Tabung' => $pelanggan->harga_tabung ? 'Rp ' . number_format($pelanggan->harga_tabung, 0, ',', '.') : 'Tidak ada',
                    ]
                ];
            }
        }
        
        // Check for Armada (license plate)
        $armada = Armada::where('nopol', $code)->first();
        if ($armada) {
            return [
                'type' => 'Armada',
                'title' => "Detail Armada - {$armada->nopol}",
                'details' => [
                    'No. Polisi' => $armada->nopol,
                    'Merk Kendaraan' => $armada->merk_kendaraan ?? 'Tidak ada',
                    'Tipe Kendaraan' => $armada->tipe_kendaraan ?? 'Tidak ada',
                    'Tahun' => $armada->tahun ?? 'Tidak ada',
                    'Warna' => $armada->warna ?? 'Tidak ada',
                    'Driver' => $armada->driver ?? 'Tidak ada',
                ]
            ];
        }
        
        return null;
    }

    /**
     * Generate popup content HTML
     */
    protected static function generatePopupContent(array $details): string
    {
        $html = "<div class='p-4'>";
        
        foreach ($details['details'] as $label => $value) {
            $html .= "<div class='mb-2'>";
            $html .= "<strong class='text-gray-700'>{$label}:</strong> ";
            $html .= "<span class='text-gray-900'>{$value}</span>";
            $html .= "</div>";
        }
        
        $html .= "</div>";
        
        return $html;
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('nama_aktivitas')
                    ->label('Aktivitas')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),

                TextColumn::make('nama_petugas')
                    ->label('Nama Petugas')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                TextColumn::make('dari')
                    ->label('Dari')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->formatStateUsing(function ($state) {
                        if (!$state) return $state;
                        
                        // Get display name based on code prefix
                        $displayName = $state;
                        if (str_starts_with($state, 'GD')) {
                            // Gudang
                            $gudang = Gudang::where('kode_gudang', $state)->first();
                            if ($gudang && $gudang->nama_gudang) {
                                $displayName = $gudang->nama_gudang;
                            }
                        } elseif (str_starts_with($state, 'PA') || str_starts_with($state, 'PU')) {
                            // Pelanggan
                            $pelanggan = Pelanggan::where('kode_pelanggan', $state)->first();
                            if ($pelanggan && $pelanggan->nama_pelanggan) {
                                $displayName = $pelanggan->nama_pelanggan;
                            }
                        }
                        
                        $details = self::getCodeDetails($state);
                        if ($details) {
                            $safeDisplayName = htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
                            $jsCode = json_encode($state);
                            
                            // Embed JavaScript jika belum ada
                            $script = '';
                            static $scriptAdded = false;
                            if (!$scriptAdded) {
                                $script = '<script>
                                if (typeof window.showCodeDetails === "undefined") {
                                    window.showCodeDetails = function(code) {
                                        if (!code) return false;
                                        fetch("/admin/get-code-details/" + encodeURIComponent(code))
                                            .then(function(response) { return response.json(); })
                                            .then(function(data) {
                                                if (data.success) {
                                                    var existing = document.getElementById("codeModal");
                                                    if (existing) existing.remove();
                                                    
                                                    // Detect dark mode
                                                    var isDarkMode = document.documentElement.classList.contains("dark") || 
                                                                   document.body.classList.contains("dark") ||
                                                                   window.matchMedia("(prefers-color-scheme: dark)").matches;
                                                    
                                                    var modal = document.createElement("div");
                                                    modal.id = "codeModal";
                                                    modal.style.cssText = "position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:999999;display:flex;align-items:center;justify-content:center;padding:20px;";
                                                    
                                                    var content = document.createElement("div");
                                                    if (isDarkMode) {
                                                        content.style.cssText = "background:#1f2937;color:#f9fafb;border-radius:8px;max-width:500px;width:100%;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.5);border:1px solid #374151;";
                                                    } else {
                                                        content.style.cssText = "background:white;color:#111827;border-radius:8px;max-width:500px;width:100%;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.3);";
                                                    }
                                                    
                                                    var headerBg = isDarkMode ? "#374151" : "#f9fafb";
                                                    var headerText = isDarkMode ? "#f9fafb" : "#111827";
                                                    var closeColor = isDarkMode ? "#9ca3af" : "#666";
                                                    var contentText = isDarkMode ? "#e5e7eb" : "#374151";
                                                    var buttonBg = isDarkMode ? "#3b82f6" : "#3b82f6";
                                                    
                                                    content.innerHTML = 
                                                        "<div style=\"display:flex;justify-content:space-between;align-items:center;padding:16px;border-bottom:1px solid " + (isDarkMode ? "#4b5563" : "#e5e7eb") + ";background:" + headerBg + ";\">" +
                                                            "<h3 style=\"margin:0;font-size:18px;font-weight:600;color:" + headerText + ";\">" + data.title + "</h3>" +
                                                            "<button onclick=\"closeCodeModal()\" style=\"background:none;border:none;font-size:24px;cursor:pointer;color:" + closeColor + ";\">&times;</button>" +
                                                        "</div>" +
                                                        "<div style=\"padding:16px;max-height:300px;overflow-y:auto;color:" + contentText + ";\">" + data.content + "</div>" +
                                                        "<div style=\"display:flex;justify-content:flex-end;padding:16px;border-top:1px solid " + (isDarkMode ? "#4b5563" : "#e5e7eb") + ";background:" + headerBg + ";\">" +
                                                            "<button onclick=\"closeCodeModal()\" style=\"background:" + buttonBg + ";color:white;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;\">Tutup</button>" +
                                                        "</div>";
                                                    
                                                    modal.appendChild(content);
                                                    modal.onclick = function(e) { if (e.target === modal) closeCodeModal(); };
                                                    document.body.appendChild(modal);
                                                } else {
                                                    alert("Detail tidak ditemukan");
                                                }
                                            }).catch(function() { alert("Terjadi kesalahan"); });
                                        return false;
                                    };
                                    window.closeCodeModal = function() {
                                        var modal = document.getElementById("codeModal");
                                        if (modal) modal.remove();
                                    };
                                }
                                </script>';
                                $scriptAdded = true;
                            }
                            
                            return new HtmlString($script . "
                                <span class='cursor-pointer text-blue-600 hover:text-blue-800 hover:underline font-medium transition-colors duration-200'
                                      onclick='showCodeDetails({$jsCode}); return false;'>
                                    {$safeDisplayName}
                                </span>
                            ");
                        }
                        
                        return $displayName;
                    }),

                TextColumn::make('tujuan')
                    ->label('Tujuan')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->formatStateUsing(function ($state) {
                        if (!$state) return $state;
                        
                        // Get display name based on code prefix
                        $displayName = $state;
                        if (str_starts_with($state, 'GD')) {
                            // Gudang
                            $gudang = Gudang::where('kode_gudang', $state)->first();
                            if ($gudang && $gudang->nama_gudang) {
                                $displayName = $gudang->nama_gudang;
                            }
                        } elseif (str_starts_with($state, 'PA') || str_starts_with($state, 'PU')) {
                            // Pelanggan
                            $pelanggan = Pelanggan::where('kode_pelanggan', $state)->first();
                            if ($pelanggan && $pelanggan->nama_pelanggan) {
                                $displayName = $pelanggan->nama_pelanggan;
                            }
                        }
                        
                        $details = self::getCodeDetails($state);
                        if ($details) {
                            $safeDisplayName = htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
                            $jsCode = json_encode($state);
                            return new HtmlString("
                                <span class='cursor-pointer text-blue-600 hover:text-blue-800 hover:underline font-medium transition-colors duration-200'
                                      onclick='showCodeDetails({$jsCode}); return false;'>
                                    {$safeDisplayName}
                                </span>
                            ");
                        }
                        
                        return $displayName;
                    }),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'Pending',
                        'danger' => 'Kosong',
                        'success' => 'Isi',
                    ])
                    ->sortable(),

                TextColumn::make('total_tabung')
                    ->label('Jumlah Tabung')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('total_volume')
                    ->label('Total Volume (m³)')
                    ->getStateUsing(function ($record) {
                        try {
                            // Ambil array kode_tabung dari kolom tabung di aktivitas_tabung
                            $tabungList = $record->tabung;
                            
                            if (empty($tabungList) || !is_array($tabungList)) {
                                return '-';
                            }
                            
                            // Flatten array - extract hanya kode_tabung string
                            $kodeTabungList = [];
                            foreach ($tabungList as $item) {
                                if (is_array($item)) {
                                    // Jika array, cari key qr_code atau kode_tabung
                                    if (isset($item['qr_code']) && is_string($item['qr_code'])) {
                                        $kodeTabungList[] = $item['qr_code'];
                                    } elseif (isset($item['kode_tabung']) && is_string($item['kode_tabung'])) {
                                        $kodeTabungList[] = $item['kode_tabung'];
                                    }
                                } elseif (is_string($item)) {
                                    // Jika string langsung, gunakan sebagai kode_tabung
                                    $kodeTabungList[] = $item;
                                }
                            }
                            
                            // Pastikan array hanya berisi string dan tidak ada duplikat
                            $kodeTabungList = array_values(array_unique(array_filter($kodeTabungList, 'is_string')));
                            
                            if (empty($kodeTabungList)) {
                                return '-';
                            }
                            
                            // Ambil total volume dari stok_tabung berdasarkan kode_tabung
                            $totalVolume = \App\Models\StokTabung::whereIn('kode_tabung', $kodeTabungList)
                                ->sum('volume');
                            
                            if ($totalVolume > 0) {
                                return number_format($totalVolume, 2, ',', '.');
                            }
                            
                            return '-';
                        } catch (\Exception $e) {
                            return '-';
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->keterangan)
                    ->placeholder('Tidak ada keterangan')
                    ->toggleable(),

                TextColumn::make('waktu')
                    ->label('Waktu Input')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Kosong' => 'Kosong',
                        'Isi' => 'Isi',
                    ])
                    ->multiple(),

                SelectFilter::make('id_user')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('tanggal_dari')
                            ->label('Tanggal Dari'),
                        DatePicker::make('tanggal_sampai')
                            ->label('Tanggal Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal_dari'],
                                fn (Builder $query, $date): Builder => $query->where('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['tanggal_sampai'],
                                fn (Builder $query, $date): Builder => $query->where('tanggal', '<=', $date),
                            );
                    }),

                SelectFilter::make('nama_aktivitas')
                    ->label('Aktivitas')
                    ->options(function () {
                        return TabungActivity::distinct()
                            ->pluck('nama_aktivitas', 'nama_aktivitas')
                            ->toArray();
                    })
                    ->multiple(),

                SelectFilter::make('dari')
                    ->label('Dari')
                    ->options(function () {
                        return TabungActivity::distinct()
                            ->pluck('dari', 'dari')
                            ->toArray();
                    })
                    ->multiple(),

                SelectFilter::make('tujuan')
                    ->label('Tujuan')
                    ->options(function () {
                        return TabungActivity::distinct()
                            ->pluck('tujuan', 'tujuan')
                            ->toArray();
                    })
                    ->multiple(),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => TabungActivityResource::getUrl('view', ['record' => $record]))
                    ->color('info'),
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => TabungActivityResource::getUrl('edit', ['record' => $record]))
                    ->color('warning'),
                Action::make('delete')
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->delete()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang dipilih'),
                ]),
            ])
            ->defaultSort('waktu', 'desc')
            ->emptyStateHeading('Belum ada aktivitas tabung')
            ->emptyStateDescription('Belum ada aktivitas tabung yang tercatat dalam sistem.')
            ->striped();
    }
}
