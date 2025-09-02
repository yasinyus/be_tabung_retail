<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Codes Tabung Gas</title>
    <style>
        @page {
            margin: 20px;
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            font-size: 11px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
        }
        .header p {
            margin: 5px 0;
            font-size: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        
        .qr-cell { 
            border: 2px solid #000; 
            padding: 10px; 
            text-align: center;
            width: 50%;
            vertical-align: top;
            height: 180px;
        }
        
        .qr-code { 
            width: 100px; 
            height: 100px; 
            margin: 0 auto 8px auto; 
            display: block;
        }
        
        .qr-placeholder {
            width: 100px; 
            height: 100px; 
            margin: 0 auto 8px auto; 
            border: 1px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            font-size: 8px;
            color: #666;
        }
        
        .info { 
            font-size: 10px; 
            line-height: 1.3;
        }
        
        .info div { 
            margin: 2px 0; 
        }
        
        .kode-tabung { 
            font-weight: bold; 
            font-size: 12px; 
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>QR Codes Tabung Gas</h2>
        <p>Generated: {{ date('d/m/Y H:i') }} | Total: {{ count($qrData) }} tabung</p>
    </div>

    <table>
        @foreach($qrData->chunk(2) as $chunk)
            <tr>
                @foreach($chunk as $data)
                    <td class="qr-cell">
                        @if($data['has_qr'] && $data['qr_base64'])
                            <img src="{{ $data['qr_base64'] }}" alt="QR Code" class="qr-code">
                            <div style="font-size: 8px; color: green;">QR: OK</div>
                        @else
                            <div class="qr-placeholder">
                                QR Not Available
                                @if(isset($data['error']))
                                    <br><small>{{ substr($data['error'], 0, 20) }}</small>
                                @endif
                            </div>
                            <div style="font-size: 8px; color: red;">
                                Debug: has_qr={{ $data['has_qr'] ? 'true' : 'false' }}, 
                                qr_exists={{ !empty($data['qr_base64']) ? 'true' : 'false' }}
                            </div>
                        @endif
                        
                        <div class="info">
                            <div class="kode-tabung">Kode Tabung: {{ $data['tabung']->kode_tabung }}</div>
                            <div>Seri Tabung: {{ $data['tabung']->seri_tabung }}</div>
                            <div>Tahun: {{ $data['tabung']->tahun }}</div>
                        </div>
                    </td>
                @endforeach
                
                {{-- Fill empty cell if odd number --}}
                @if(count($chunk) == 1)
                    <td class="qr-cell" style="border: none;"></td>
                @endif
            </tr>
        @endforeach
    </table>
</body>
</html>
