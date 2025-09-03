<?php

namespace App\Filament\Resources\Tagihans;

use App\Filament\Resources\Tagihans\Pages\ListTagihans;
use App\Models\Tagihan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use App\Models\Transaction;
use App\Models\SaldoPelanggan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Tagihan';

    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'nama_pelanggan';

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Tagihan::query()
                    ->leftJoin('saldo_pelanggans', 'pelanggans.kode_pelanggan', '=', 'saldo_pelanggans.kode_pelanggan')
                    ->select('pelanggans.*', 'saldo_pelanggans.saldo')
            )
            ->columns([
                TextColumn::make('row_number')
                    ->label('No')
                    ->rowIndex(),
                    
                TextColumn::make('kode_pelanggan')
                    ->label('Kode Pelanggan')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('saldo')
                    ->label('Saldo')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($record) => $record->saldo < 0 ? 'danger' : ($record->saldo > 0 ? 'success' : 'gray')),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('saldo_minus')
                    ->label('Saldo Minus')
                    ->query(fn (Builder $query): Builder => $query->where('saldo_pelanggans.saldo', '<', 0)),
                    
                Filter::make('saldo_plus')
                    ->label('Saldo Plus')
                    ->query(fn (Builder $query): Builder => $query->where('saldo_pelanggans.saldo', '>', 0)),
                    
                Filter::make('saldo_nol')
                    ->label('Saldo Nol')
                    ->query(fn (Builder $query): Builder => $query->where('saldo_pelanggans.saldo', '=', 0)),
            ])
            ->actions([
                Action::make('tagihan')
                    ->label('Tagihan')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('warning')
                    ->form([
                        Hidden::make('kode_pelanggan')
                            ->default(fn ($record) => $record->kode_pelanggan),
                            
                        Hidden::make('nama_pelanggan')
                            ->default(fn ($record) => $record->nama_pelanggan),
                            
                        TextInput::make('kode_pelanggan_display')
                            ->label('Kode Pelanggan')
                            ->default(fn ($record) => $record->kode_pelanggan)
                            ->disabled(),
                            
                        TextInput::make('nama_pelanggan_display')
                            ->label('Nama Pelanggan')
                            ->default(fn ($record) => $record->nama_pelanggan)
                            ->disabled(),
                            
                        TextInput::make('jumlah_tabung')
                            ->label('Jumlah Tabung')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get, $record) {
                                $harga_satuan = $record->harga_tabung ?? 0;
                                $total = $state * $harga_satuan;
                                $set('total_harga', $total);
                            }),
                            
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->default(now())
                            ->required(),
                            
                        TextInput::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->default(fn ($record) => $record->harga_tabung ?? 0)
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                            
                        TextInput::make('total_harga')
                            ->label('Total Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                            
                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Cash',
                                'transfer' => 'Transfer',
                            ])
                            ->required()
                            ->default('cash'),
                            
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'paid' => 'Paid',
                                'pending' => 'Pending',
                                'canceled' => 'Canceled',
                            ])
                            ->required()
                            ->default('pending'),
                            
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Catatan tambahan untuk transaksi ini')
                            ->maxLength(500),
                            
                        Hidden::make('user_id')
                            ->default(Auth::id()),
                    ])
                    ->action(function (array $data, $record) {
                        $jumlah_tabung = $data['jumlah_tabung'];
                        $harga_satuan = $record->harga_tabung ?? 0;
                        $total_harga = $jumlah_tabung * $harga_satuan;
                        
                        // Simpan transaksi
                        Transaction::create([
                            'trx_id' => 'TRX-' . strtoupper(uniqid()),
                            'user_id' => Auth::id(),
                            'customer_id' => $record->id,
                            'transaction_date' => $data['transaction_date'],
                            'type' => 'purchase',
                            'total' => $total_harga,
                            'description' => "Pembelian {$jumlah_tabung} tabung @ Rp " . number_format($harga_satuan, 0, ',', '.'),
                            'status' => $data['status'],
                            'notes' => "Harga Satuan: Rp " . number_format($harga_satuan, 0, ',', '.') . "\n" .
                                      "Jumlah Tabung: {$jumlah_tabung}\n" .
                                      "Payment Method: {$data['payment_method']}\n" .
                                      ($data['notes'] ? "Catatan: " . $data['notes'] : ''),
                        ]);
                        
                        // Update saldo pelanggan (kurangi saldo dengan total harga)
                        $saldoPelanggan = SaldoPelanggan::where('kode_pelanggan', $record->kode_pelanggan)->first();
                        if ($saldoPelanggan) {
                            $saldo_lama = $saldoPelanggan->saldo;
                            $saldo_baru = $saldo_lama - $total_harga;
                            $saldoPelanggan->update(['saldo' => $saldo_baru]);
                        } else {
                            // Jika belum ada record saldo, buat baru dengan saldo minus
                            SaldoPelanggan::create([
                                'kode_pelanggan' => $record->kode_pelanggan,
                                'saldo' => -$total_harga
                            ]);
                        }
                        
                        Notification::make()
                            ->title('Transaksi berhasil dibuat')
                            ->body("Transaksi pembelian {$jumlah_tabung} tabung sebesar Rp " . number_format($total_harga, 0, ',', '.') . " berhasil disimpan. Saldo pelanggan telah diupdate.")
                            ->success()
                            ->send();
                    }),
                    
                Action::make('lihat')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->form([
                        TextInput::make('kode_pelanggan')
                            ->label('Kode Pelanggan')
                            ->default(fn ($record) => $record->kode_pelanggan)
                            ->disabled(),
                            
                        TextInput::make('nama_pelanggan')
                            ->label('Nama Pelanggan')
                            ->default(fn ($record) => $record->nama_pelanggan)
                            ->disabled(),
                            
                        // TextInput::make('alamat')
                        //     ->label('Alamat')
                        //     ->default(fn ($record) => $record->alamat)
                        //     ->disabled(),
                            
                        // TextInput::make('telepon')
                        //     ->label('Telepon')
                        //     ->default(fn ($record) => $record->telepon)
                        //     ->disabled(),
                            
                        TextInput::make('harga_tabung')
                            ->label('Harga Tabung')
                            ->default(fn ($record) => 'Rp ' . number_format($record->harga_tabung, 0, ',', '.'))
                            ->disabled(),
                            
                        TextInput::make('saldo_display')
                            ->label('Saldo')
                            ->default(fn ($record) => 'Rp ' . number_format($record->saldo, 0, ',', '.'))
                            ->disabled(),
                    ])
                    ->modalHeading('Detail Pelanggan')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->defaultSort('saldo', 'asc'); // Sort by saldo ascending (minus first)
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTagihans::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false; // Disable create since this is read-only view
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['admin_utama', 'keuangan']);
    }
}
