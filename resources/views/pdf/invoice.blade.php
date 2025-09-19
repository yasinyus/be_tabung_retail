<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $pelanggan->nama_pelanggan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 10px;
            font-size: 11px;
            color: #666;
        }
        
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .invoice-info > div {
            width: 48%;
        }
        
        .invoice-info h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .invoice-info p {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .invoice-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .invoice-details h3 {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #333;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px dashed #ddd;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
        }
        
        .detail-label {
            font-weight: 500;
        }
        
        .detail-value {
            text-align: right;
        }
        
        .tabung-list {
            margin-bottom: 30px;
        }
        
        .tabung-list h3 {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .tabung-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .tabung-table th,
        .tabung-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .tabung-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        
        .tabung-table td {
            font-size: 10px;
        }
        
        .tabung-table .text-center {
            text-align: center;
        }
        
        .tabung-table .text-right {
            text-align: right;
        }
        
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 3px 0;
        }
        
        .summary-row.total {
            border-top: 1px solid #333;
            padding-top: 10px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 13px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status.confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status.pending {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <strong>PT CEI</strong><br>
        </div>
        <h1>INVOICE</h1>
        {{-- <h2>No: INV-{{ $laporan->id }}-{{ date('Y') }}</h2> --}}
    </div>

    <!-- Invoice Info -->
    <div class="invoice-info">
        <div>
            <h3>Data Pelanggan</h3>
            <p><strong>Nama:</strong> {{ $pelanggan->nama_pelanggan }}</p>
            <p><strong>Kode:</strong> {{ $pelanggan->kode_pelanggan }}</p>
            @if($pelanggan->alamat)
                <p><strong>Alamat:</strong> {{ $pelanggan->alamat }}</p>
            @endif
            @if($pelanggan->telepon)
                <p><strong>Telepon:</strong> {{ $pelanggan->telepon }}</p>
            @endif
            @if($pelanggan->jenis_pelanggan)
                <p><strong>Jenis:</strong> {{ ucfirst($pelanggan->jenis_pelanggan) }}</p>
            @endif
        </div>
        
        <div>
            <h3>Info Invoice</h3>
            <p><strong>Tanggal:</strong> {{ $laporan->tanggal->format('d/m/Y') }}</p>
            <p><strong>Dicetak:</strong> {{ now()->format('d/m/Y H:i') }}</p>
            <p><strong>Status:</strong> 
                <span class="status {{ $laporan->konfirmasi ? 'confirmed' : 'pending' }}">
                    {{ $laporan->konfirmasi ? 'DIKONFIRMASI' : 'PENDING' }}
                </span>
            </p>
        </div>
    </div>

     <!-- List Tabung -->
    @if($listTabung && $listTabung->count() > 0)
        <div class="tabung-list">
            <h3>Daftar Tabung</h3>
            <table class="tabung-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Tabung</th>
                        {{-- <th>Volume</th>
                        <th>Jenis</th>
                        <th>Brand</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Harga Satuan</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @php $totalTabung = 0; @endphp
                    @foreach($listTabung as $index => $tabung)
                        @php $harga = $tabung->harga_jual ?? 0; $totalTabung += $harga; @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $tabung->kode_tabung }}</td>
                            {{-- <td>{{ $tabung->volume ?? '-' }}{{ $tabung->volume ? 'kg' : '' }}</td>
                            <td>{{ $tabung->jenis_tabung ?? 'Gas LPG' }}</td>
                            <td>{{ $tabung->brand ?? '-' }}</td>
                            <td class="text-center">{{ $tabung->status_tabung ?? 'Terjual' }}</td>
                            <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Summary Tabung -->
            <div class="summary">
                <div class="summary-row">
                    <span>Total Tabung:</span>
                    <span>{{ $listTabung->count() }} unit</span>
                </div>
                <div class="summary-row total">
                    <span>Total Penjualan Tabung:</span>
                    <span>Rp {{ number_format($totalTabung, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Invoice Details -->
    <div class="invoice-details">
        <h3>Detail Transaksi</h3>
        <div class="detail-row">
            <span class="detail-label">Keterangan:</span>
            <span class="detail-value">{{ $laporan->keterangan }}</span>
        </div>
        @if($laporan->tabung)
            <div class="detail-row">
                <span class="detail-label">Jumlah Tabung:</span>
                <span class="detail-value">{{ $laporan->tabung }} unit</span>
            </div>
        @endif
        @if($laporan->harga)
            <div class="detail-row">
                <span class="detail-label">Harga Transaksi:</span>
                <span class="detail-value">Rp {{ number_format($laporan->harga, 0, ',', '.') }}</span>
            </div>
        @endif
        @if($laporan->tambahan_deposit)
            <div class="detail-row">
                <span class="detail-label">Tambahan Deposit:</span>
                <span class="detail-value" style="color: green;">+Rp {{ number_format($laporan->tambahan_deposit, 0, ',', '.') }}</span>
            </div>
        @endif
        @if($laporan->pengurangan_deposit)
            <div class="detail-row">
                <span class="detail-label">Pengurangan Deposit:</span>
                <span class="detail-value" style="color: red;">-Rp {{ number_format($laporan->pengurangan_deposit, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Sisa Deposit:</span>
            <span class="detail-value"><strong>Rp {{ number_format($laporan->sisa_deposit, 0, ',', '.') }}</strong></span>
        </div>
    </div>

   

    <!-- Footer -->
    <div class="footer">
        <p><em>Invoice ini digenerate secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}</em></p>
        <p>Terima kasih atas kepercayaan Anda menggunakan layanan PT CEI</p>
    </div>
</body>
</html>
