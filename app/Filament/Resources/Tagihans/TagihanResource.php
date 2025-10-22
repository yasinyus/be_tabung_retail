<?php

namespace App\Filament\Resources\Tagihans;

use App\Filament\Resources\Tagihans\Pages\ListTagihans;
use App\Filament\Resources\Tagihans\Pages;
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
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use App\Models\Transaction;
use App\Models\DetailTransaksi;
use App\Models\LaporanPelanggan;
use App\Models\SaldoPelanggan;
use App\Models\Deposit;
use App\Models\SerahTerimaTabung;
use App\Filament\Resources\Deposits\DepositResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Transaksi Pelanggan';
    
    protected static ?string $modelLabel = 'Transaksi Pelanggan';
    
    protected static ?string $pluralModelLabel = 'Transaksi Pelanggan';

    protected static ?int $navigationSort = 9;

    protected static ?string $recordTitleAttribute = 'nama_pelanggan';

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Tagihan::query()
                    ->leftJoin('saldo_pelanggans', 'pelanggans.kode_pelanggan', '=', 'saldo_pelanggans.kode_pelanggan')
                    ->select(
                        'pelanggans.*', 
                        'saldo_pelanggans.saldo as saldo_amount',
                        'saldo_pelanggans.id as saldo_id'
                    )
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
                    
                TextColumn::make('saldo_amount')
                    ->label('Saldo')
                    ->money('IDR')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->saldo_amount ?? 0)
                    ->color(function ($record) {
                        $saldo = $record->saldo_amount ?? 0;
                        return $saldo < 0 ? 'danger' : ($saldo > 0 ? 'success' : 'gray');
                    }),
                    
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
                    
                Filter::make('belum_ada_saldo')
                    ->label('Belum Ada Data Saldo')
                    ->query(fn (Builder $query): Builder => $query->whereNull('saldo_pelanggans.saldo')),
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
                        
                        TextInput::make('volume')
                            ->label('Volume (m³)')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->placeholder('Masukkan total volume')
                            ->helperText('Opsional: Total volume untuk transaksi ini')
                            ->suffix('m³'),
                            
                        TextInput::make('total_harga')
                            ->label('Total Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(0)
                            ->default(0)
                            ->placeholder('Masukkan total harga (bisa 0)')
                            ->helperText('Masukkan total harga untuk transaksi ini. Boleh 0 untuk transaksi tanpa biaya.'),
                            
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->default(now())
                            ->required(),
                            
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
                        // Ambil total harga dari input (boleh 0)
                        $total_harga = $data['total_harga'] ?? 0;
                        
                        // Cek saldo pelanggan terlebih dahulu
                        $saldoPelanggan = SaldoPelanggan::where('kode_pelanggan', $record->kode_pelanggan)->first();
                        $saldo_saat_ini = $saldoPelanggan ? $saldoPelanggan->saldo : 0;
                        
                        // Simpan transaksi
                        $transaction = Transaction::create([
                            'trx_id' => 'TRX-' . strtoupper(uniqid()),
                            'user_id' => Auth::id(),
                            'customer_id' => $record->id,
                            'transaction_date' => $data['transaction_date'],
                            'type' => 'purchase',
                            'total' => $total_harga,
                            'harga' => 0,
                            'jumlah_tabung' => 0,
                            'Payment Method' => $data['payment_method'],
                            'status' => $data['status'],
                            'notes' => $data['notes'] ?? null,
                        ]);
                        
                        // Simpan detail transaksi dengan volume jika ada
                        if (!empty($data['volume']) && $data['volume'] > 0) {
                            // Simpan volume tanpa breakdown per tabung
                            DetailTransaksi::create([
                                'trx_id' => $transaction->trx_id,
                                'tabung' => [[
                                    'kode_tabung' => 'VOLUME-ONLY',
                                    'volume' => $data['volume']
                                ]],
                                'keterangan' => 'Volume total: ' . $data['volume'] . ' m³',
                            ]);
                        }
                        
                        // Hitung saldo baru berdasarkan apakah ada harga atau tidak
                        $saldo_baru = $saldo_saat_ini;
                        $pengurangan_deposit = 0;
                        
                        if ($total_harga > 0) {
                            // Jika ada harga, kurangi saldo
                            $saldo_baru = $saldo_saat_ini - $total_harga;
                            $pengurangan_deposit = $total_harga;
                            
                            // Update atau create saldo pelanggan
                            if ($saldoPelanggan) {
                                $saldoPelanggan->update(['saldo' => $saldo_baru]);
                            } else {
                                SaldoPelanggan::create([
                                    'kode_pelanggan' => $record->kode_pelanggan,
                                    'saldo' => $saldo_baru
                                ]);
                            }
                        }
                        
                        // Insert ke tabel laporan pelanggan
                        LaporanPelanggan::create([
                            'tanggal' => $data['transaction_date'],
                            'kode_pelanggan' => $record->kode_pelanggan,
                            'keterangan' => 'Tagihan - ' . ($data['notes'] ?? 'Transaksi tagihan'),
                            'list_tabung' => null,
                            'tabung' => 0,
                            'harga' => $total_harga,
                            'tambahan_deposit' => 0,
                            'pengurangan_deposit' => $pengurangan_deposit,
                            'sisa_deposit' => $saldo_baru,
                            'konfirmasi' => $data['status'] === 'paid' ? true : false,
                            'id_bast_invoice' => '-',
                        ]);
                        
                        // Notifikasi sukses
                        $notificationBody = "Transaksi tagihan sebesar Rp " . number_format($total_harga, 0, ',', '.') . " berhasil disimpan.\n";
                        if ($data['volume']) {
                            $notificationBody .= "Volume: " . number_format($data['volume'], 2, ',', '.') . " m³\n";
                        }
                        $notificationBody .= "Saldo sebelumnya: Rp " . number_format($saldo_saat_ini, 0, ',', '.') . "\n";
                        $notificationBody .= "Saldo sekarang: Rp " . number_format($saldo_baru, 0, ',', '.');
                        
                        if ($total_harga == 0) {
                            $notificationBody .= "\n(Tidak ada perubahan saldo karena harga = 0)";
                        }
                        
                        Notification::make()
                            ->title('Transaksi Berhasil Dibuat')
                            ->body($notificationBody)
                            ->success()
                            ->send();
                    }),
                    
                Action::make('laporan')
                    ->label('Laporan')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn ($record) => route('filament.admin.resources.tagihans.laporan', ['kode_pelanggan' => $record->kode_pelanggan]))
                    ->openUrlInNewTab(false),
                    
                Action::make('lihat')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->form([
                        
                            
                        ViewField::make('pelanggan_history')
                            ->label('')
                            ->view('filament.components.pelanggan-history')
                            ->viewData(function ($record) {
                                // Get deposit history
                                $deposits = Deposit::where('kode_pelanggan', $record->kode_pelanggan)
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                                
                                // Get transaction history - menggunakan relationship customer dan load detail transaksi
                                $transactions = Transaction::with(['customer', 'detailTransaksi'])
                                    ->whereHas('customer', function($query) use ($record) {
                                        $query->where('kode_pelanggan', $record->kode_pelanggan);
                                    })
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                                
                                return [
                                    'deposits' => $deposits,
                                    'transactions' => $transactions,
                                    'pelanggan' => $record
                                ];
                            })
                            ->columnSpanFull(),
                    ])
                    ->modalHeading(fn ($record) => 'Detail Pelanggan - ' . $record->nama_pelanggan)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('7xl'),
                    
                Action::make('tambah_deposit')
                    ->label('Tambah Deposit')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
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
                            
                        TextInput::make('saldo')
                            ->label('Jumlah Deposit')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(1)
                            ->placeholder('Masukkan jumlah deposit'),
                            
                        DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),
                            
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Catatan tambahan untuk deposit ini...')
                            ->rows(3),
                    ])
                    ->action(function (array $data, $record) {
                        // Import model yang diperlukan
                        $depositData = [
                            'pelanggan_id' => $record->id,
                            'kode_pelanggan' => $data['kode_pelanggan'],
                            'nama_pelanggan' => $data['nama_pelanggan'],
                            'saldo' => $data['saldo'],
                            'tanggal' => $data['tanggal'],
                            'keterangan' => $data['keterangan'] ?? null,
                        ];
                        
                        // Buat deposit baru - model observer akan otomatis update saldo
                        Deposit::create($depositData);
                        
                        // Refresh data dan get saldo terbaru setelah deposit ditambahkan
                        $saldoPelanggan = SaldoPelanggan::where('kode_pelanggan', $data['kode_pelanggan'])->first();
                        
                        // Jika belum ada record saldo, buat baru
                        if (!$saldoPelanggan) {
                            $saldoPelanggan = SaldoPelanggan::create([
                                'kode_pelanggan' => $data['kode_pelanggan'],
                                'saldo' => $data['saldo']
                            ]);
                        }
                        
                        $sisaDeposit = $saldoPelanggan->saldo;
                        
                        // Insert data ke tabel laporan_pelanggan
                        LaporanPelanggan::create([
                            'tanggal' => $data['tanggal'],
                            'kode_pelanggan' => $data['kode_pelanggan'],
                            'keterangan' => 'Deposit',
                            'tabung' => 0,
                            'harga' => 0,
                            'tambahan_deposit' => $data['saldo'],
                            'pengurangan_deposit' => 0,
                            'sisa_deposit' => $sisaDeposit,
                            'konfirmasi' => false,
                            'list_tabung' => [], // Tambahkan field list_tabung dengan array kosong
                        ]);
                        
                        // Notifikasi sukses
                        Notification::make()
                            ->title('Deposit Berhasil Ditambahkan!')
                            ->body(
                                "Deposit sebesar Rp " . number_format($data['saldo'], 0, ',', '.') . 
                                " berhasil ditambahkan untuk pelanggan " . $record->nama_pelanggan
                            )
                            ->success()
                            ->send();
                            
                        // Redirect ke halaman tagihan
                        return redirect()->to('/admin/tagihans');
                    })
                    ->modalHeading(fn ($record) => 'Tambah Deposit - ' . $record->nama_pelanggan)
                    ->modalSubmitActionLabel('Simpan Deposit')
                    ->modalWidth('md'),
                    
                Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->form([
                        ViewField::make('refund_table')
                            ->view('filament.components.refund-table')
                            ->viewData(function ($record) {
                                // Get refund data for this customer with status 'Rusak'
                                $refundData = SerahTerimaTabung::where('kode_pelanggan', $record->kode_pelanggan)
                                    ->where('status', 'Rusak')
                                    ->with('pelanggan')
                                    ->get();
                                    
                                return [
                                    'kode_pelanggan' => $record->kode_pelanggan,
                                    'nama_pelanggan' => $record->nama_pelanggan,
                                    'refund_data' => $refundData,
                                ];
                            })
                    ])
                    ->modalHeading(fn ($record) => 'Data Refund Tabung - ' . $record->nama_pelanggan)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('7xl'),
            ])
            ->defaultSort('saldo', 'asc'); // Sort by saldo ascending (minus first)
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTagihans::route('/'),
            'laporan' => Pages\LaporanPelanggan::route('/laporan'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false; // Disable create since this is read-only view
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return $user && in_array($user->role, ['admin_utama', 'keuangan']);
    }
}
