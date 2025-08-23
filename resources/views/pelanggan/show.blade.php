<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pelanggan - {{ $pelanggan->kode_pelanggan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="robots" content="noindex, nofollow">
</head>
<body class="bg-gradient-to-br from-purple-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-purple-600 rounded-full mb-4">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Detail Pelanggan</h1>
            <p class="text-gray-600">Informasi Pelanggan PT GAS Tabung Retail</p>
        </div>

        <!-- Main Card -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">{{ $pelanggan->kode_pelanggan }}</h2>
                            <p class="text-purple-100">{{ $pelanggan->nama_pelanggan }}</p>
                        </div>
                        <div class="text-right">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                                <div class="text-sm text-purple-100">ID Pelanggan</div>
                                <div class="text-lg font-bold">#{{ str_pad($pelanggan->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Jenis Pelanggan Badge -->
                    <div class="mt-4">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($pelanggan->jenis_pelanggan === 'agen') bg-green-100 text-green-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            @if($pelanggan->jenis_pelanggan === 'agen')
                                <i class="fas fa-crown mr-1"></i> Agen
                            @else
                                <i class="fas fa-user mr-1"></i> Umum
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Info Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">
                                <i class="fas fa-info-circle text-purple-600 mr-2"></i>
                                Informasi Pelanggan
                            </h3>
                            
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-tag text-purple-500 w-5 mr-3"></i>
                                        Kode Pelanggan
                                    </span>
                                    <span class="font-semibold bg-purple-50 text-purple-700 px-3 py-1 rounded-full">
                                        {{ $pelanggan->kode_pelanggan }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-building text-blue-500 w-5 mr-3"></i>
                                        Nama Pelanggan
                                    </span>
                                    <span class="font-semibold text-gray-800 text-right">
                                        {{ $pelanggan->nama_pelanggan }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-envelope text-green-500 w-5 mr-3"></i>
                                        Email
                                    </span>
                                    <span class="font-semibold text-gray-800">
                                        {{ $pelanggan->email }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-money-bill-wave text-orange-500 w-5 mr-3"></i>
                                        Harga Tabung
                                    </span>
                                    <span class="font-semibold bg-orange-50 text-orange-700 px-3 py-1 rounded-full">
                                        {{ $pelanggan->formatted_harga }}
                                    </span>
                                </div>

                                @if($pelanggan->penanggung_jawab)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-user-tie text-indigo-500 w-5 mr-3"></i>
                                        Penanggung Jawab
                                    </span>
                                    <span class="font-semibold text-gray-800">
                                        {{ $pelanggan->penanggung_jawab }}
                                    </span>
                                </div>
                                @endif

                                <div class="py-3">
                                    <span class="text-gray-600 flex items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-red-500 w-5 mr-3"></i>
                                        Lokasi
                                    </span>
                                    <div class="bg-gray-50 rounded-lg p-3 text-gray-700">
                                        {{ $pelanggan->lokasi_pelanggan }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">
                                <i class="fas fa-qrcode text-purple-600 mr-2"></i>
                                QR Code
                            </h3>
                            
                            <div class="text-center">
                                @if($pelanggan->qr_code)
                                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6 inline-block">
                                        <img src="data:image/svg+xml;base64,{{ $pelanggan->getQrCodeBase64() }}" 
                                             alt="QR Code {{ $pelanggan->kode_pelanggan }}" 
                                             class="mx-auto"
                                             style="width: 200px; height: 200px;">
                                    </div>
                                    <p class="text-sm text-gray-500 mt-3">
                                        <i class="fas fa-mobile-alt mr-1"></i>
                                        Scan untuk melihat detail pelanggan
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
                            Terdaftar: {{ $pelanggan->created_at->format('d F Y, H:i') }}
                        </div>
                        
                        @if($pelanggan->created_at != $pelanggan->updated_at)
                        <div class="flex items-center">
                            <i class="fas fa-edit text-gray-400 mr-2"></i>
                            Diperbarui: {{ $pelanggan->updated_at->format('d F Y, H:i') }}
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
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <i class="fas fa-print mr-2"></i>
                    Cetak
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12 text-gray-500">
            <p class="text-sm">© {{ date('Y') }} PT GAS Tabung Retail - Sistem Manajemen Pelanggan</p>
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
