<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tabung - {{ $tabung->kode_tabung }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 text-white p-6 text-center">
                <h1 class="text-2xl font-bold">PT GAS - Tabung Retail</h1>
                <p class="text-blue-100 mt-2">Detail Informasi Tabung</p>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- QR Code Display -->
                <div class="text-center mb-6">
                    <img src="data:image/svg+xml;base64,{{ $tabung->getQrCodeBase64() }}" 
                         alt="QR Code {{ $tabung->kode_tabung }}" 
                         class="mx-auto border rounded-lg shadow-sm"
                         style="max-width: 200px; width: 100%;">
                </div>

                <!-- Tabung Information -->
                <div class="space-y-4">
                    <div class="border-b pb-3">
                        <label class="text-sm font-medium text-gray-500">Kode Tabung</label>
                        <p class="text-lg font-bold text-gray-900">{{ $tabung->kode_tabung }}</p>
                    </div>

                    <div class="border-b pb-3">
                        <label class="text-sm font-medium text-gray-500">Seri Tabung</label>
                        <p class="text-lg text-gray-900">{{ $tabung->seri_tabung }}</p>
                    </div>

                    <div class="border-b pb-3">
                        <label class="text-sm font-medium text-gray-500">Tahun Produksi</label>
                        <p class="text-lg text-gray-900">{{ $tabung->tahun }}</p>
                    </div>

                    @if($tabung->keterangan)
                    <div class="border-b pb-3">
                        <label class="text-sm font-medium text-gray-500">Keterangan</label>
                        <p class="text-gray-900">{{ $tabung->keterangan }}</p>
                    </div>
                    @endif

                    <div class="pt-2">
                        <label class="text-sm font-medium text-gray-500">Tanggal Dibuat</label>
                        <p class="text-gray-900">{{ $tabung->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="mt-6 text-center">
                    @php
                        $yearDiff = date('Y') - $tabung->tahun;
                        if ($yearDiff > 10) {
                            $statusColor = 'bg-red-100 text-red-800';
                            $statusText = 'Perlu Inspeksi';
                        } elseif ($yearDiff > 5) {
                            $statusColor = 'bg-yellow-100 text-yellow-800';
                            $statusText = 'Perhatian';
                        } else {
                            $statusColor = 'bg-green-100 text-green-800';
                            $statusText = 'Kondisi Baik';
                        }
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                        {{ $statusText }}
                    </span>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4">
                <p class="text-xs text-gray-500 text-center">
                    © {{ date('Y') }} PT GAS - Tabung Retail. Semua hak dilindungi.
                </p>
                <p class="text-xs text-gray-400 text-center mt-1">
                    Scan dilakukan pada: {{ now()->format('d M Y H:i:s') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
