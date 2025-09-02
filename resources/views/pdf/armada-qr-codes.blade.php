<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Codes Armada - {{ date('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 20px;
        }
        
        .qr-item {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
        }
        
        .qr-code {
            margin-bottom: 15px;
        }
        
        .qr-code img {
            width: 150px;
            height: 150px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: block;
            margin: 0 auto;
        }
        
        .qr-fallback {
            width: 150px;
            height: 150px;
            border: 2px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border-radius: 5px;
            background: #f5f5f5;
            flex-direction: column;
        }
        
        .qr-text {
            margin-top: 10px;
            font-family: monospace;
            font-size: 12px;
            color: #666;
            text-align: center;
            font-weight: bold;
        }
        
        .vehicle-info {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        .vehicle-info h3 {
            color: #007bff;
            margin: 0 0 10px 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .vehicle-details {
            text-align: left;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px dotted #ddd;
        }
        
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        
        .detail-value {
            color: #333;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 10px;
            color: #999;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            .qr-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📱 QR CODES ARMADA</h1>
        <p>Tanggal Generate: {{ date('d/m/Y H:i:s') }}</p>
        <p>Total Armada: {{ count($qrData) }} unit</p>
    </div>

    <div class="qr-grid">
        @foreach($qrData as $index => $data)
            @if($index > 0 && $index % 4 == 0)
                </div>
                <div class="page-break"></div>
                <div class="qr-grid">
            @endif
            
            <div class="qr-item">
                <div class="qr-code">
                    @if(isset($data['qr_base64']) && $data['qr_base64'])
                        <!-- QR Code dari model Armada -->
                        <div style="width: 150px; height: 150px; margin: 0 auto; border: 1px solid #ddd; border-radius: 5px; padding: 5px; background: white;">
                            <img src="{{ $data['qr_base64'] }}" alt="QR Code {{ $data['armada']->nopol }}" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                    @else
                        <!-- Fallback jika QR code tidak ada -->
                        <div style="width: 150px; height: 150px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; margin: 0 auto; border-radius: 5px; background: #f5f5f5; flex-direction: column;">
                            <span style="color: #999; font-size: 12px; text-align: center;">QR Code</span>
                            <span style="color: #333; font-size: 14px; font-weight: bold;">{{ $data['armada']->nopol }}</span>
                        </div>
                    @endif
                    
                    <div class="qr-text">
                        Nopol: {{ $data['qr_text'] }}
                    </div>
                </div>
                
                <div class="vehicle-info">
                    <h3>🚛 {{ strtoupper($data['armada']->nopol) }}</h3>
                    
                    <div class="vehicle-details">
                        <div class="detail-row">
                            <span class="detail-label">Nopol:</span>
                            <span class="detail-value">{{ strtoupper($data['armada']->nopol) }}</span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Kapasitas:</span>
                            <span class="detail-value">{{ $data['armada']->kapasitas ?? 'N/A' }} ton</span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Tahun:</span>
                            <span class="detail-value">{{ $data['armada']->tahun ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Jenis:</span>
                            <span class="detail-value">{{ ucfirst($data['armada']->jenis_kendaraan ?? 'Tidak diketahui') }}</span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">
                                @if($data['armada']->status == 'active')
                                    ✅ Aktif
                                @elseif($data['armada']->status == 'maintenance')
                                    🔧 Maintenance
                                @else
                                    ❌ Tidak Aktif
                                @endif
                            </span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Dibuat:</span>
                            <span class="detail-value">{{ $data['armada']->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="footer">
        Generated by Admin Tabung System - {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
