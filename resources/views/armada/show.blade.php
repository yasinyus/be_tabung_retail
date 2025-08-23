<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Armada - {{ $armada->nopol }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="robots" content="noindex, nofollow">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 rounded-full mb-4">
                <i class="fas fa-truck text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Detail Armada</h1>
            <p class="text-gray-600">Informasi Kendaraan PT GAS Tabung Retail</p>
        </div>

        <!-- Main Card -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">{{ $armada->nopol }}</h2>
                            <p class="text-blue-100">Kendaraan Operasional</p>
                        </div>
                        <div class="text-right">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                                <div class="text-sm text-blue-100">ID Armada</div>
                                <div class="text-lg font-bold">#{{ str_pad($armada->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Info Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                Informasi Kendaraan
                            </h3>
                            
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-car text-blue-500 w-5 mr-3"></i>
                                        Nomor Polisi
                                    </span>
                                    <span class="font-semibold text-gray-800 bg-gray-50 px-3 py-1 rounded-full">
                                        {{ $armada->nopol }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-weight-hanging text-green-500 w-5 mr-3"></i>
                                        Kapasitas
                                    </span>
                                    <span class="font-semibold bg-green-50 text-green-700 px-3 py-1 rounded-full">
                                        {{ $armada->kapasitas }} ton
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-calendar-alt text-purple-500 w-5 mr-3"></i>
                                        Tahun Pembuatan
                                    </span>
                                    <span class="font-semibold px-3 py-1 rounded-full
                                        @if($armada->tahun < date('Y') - 15) bg-red-50 text-red-700
                                        @elseif($armada->tahun < date('Y') - 10) bg-yellow-50 text-yellow-700
                                        @else bg-green-50 text-green-700
                                        @endif">
                                        {{ $armada->tahun }}
                                        @if($armada->tahun < date('Y') - 15)
                                            <i class="fas fa-exclamation-triangle ml-1 text-red-500"></i>
                                        @elseif($armada->tahun < date('Y') - 10)
                                            <i class="fas fa-exclamation-circle ml-1 text-yellow-500"></i>
                                        @endif
                                    </span>
                                </div>

                                @if($armada->keterangan)
                                <div class="py-3">
                                    <span class="text-gray-600 flex items-center mb-2">
                                        <i class="fas fa-sticky-note text-orange-500 w-5 mr-3"></i>
                                        Keterangan
                                    </span>
                                    <div class="bg-gray-50 rounded-lg p-3 text-gray-700">
                                        {{ $armada->keterangan }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- QR Code Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">
                                <i class="fas fa-qrcode text-blue-600 mr-2"></i>
                                QR Code
                            </h3>
                            
                            <div class="text-center">
                                @if($armada->qr_code)
                                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6 inline-block">
                                        <img src="data:image/svg+xml;base64,{{ $armada->getQrCodeBase64() }}" 
                                             alt="QR Code {{ $armada->nopol }}" 
                                             class="mx-auto"
                                             style="width: 200px; height: 200px;">
                                    </div>
                                    <p class="text-sm text-gray-500 mt-3">
                                        <i class="fas fa-mobile-alt mr-1"></i>
                                        Scan untuk melihat detail armada
                                    </p>
                                @else
                                    <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-8">
                                        <i class="fas fa-hourglass-half text-gray-400 text-3xl mb-3"></i>
                                        <p class="text-gray-500">QR Code sedang diproses...</p>
                                        <p class="text-sm text-gray-400 mt-1">Silakan refresh halaman dalam beberapa saat</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t">
                    <div class="flex flex-col sm:flex-row justify-between items-center text-sm text-gray-600">
                        <div class="flex items-center mb-2 sm:mb-0">
                            <i class="fas fa-calendar text-gray-400 mr-2"></i>
                            Dibuat: {{ $armada->created_at->format('d F Y, H:i') }}
                        </div>
                        
                        @if($armada->created_at != $armada->updated_at)
                        <div class="flex items-center">
                            <i class="fas fa-edit text-gray-400 mr-2"></i>
                            Diperbarui: {{ $armada->updated_at->format('d F Y, H:i') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 text-center space-x-4">
                <button onclick="window.history.back()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </button>
                
                <button onclick="window.print()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <i class="fas fa-print mr-2"></i>
                    Cetak
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12 text-gray-500">
            <p class="text-sm">© {{ date('Y') }} PT GAS Tabung Retail - Sistem Manajemen Armada</p>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body { 
                background: white !important; 
                print-color-adjust: exact;
            }
            .no-print { 
                display: none !important; 
            }
            .container {
                max-width: none !important;
                padding: 0 !important;
            }
        }
    </style>
</body>
</html>
