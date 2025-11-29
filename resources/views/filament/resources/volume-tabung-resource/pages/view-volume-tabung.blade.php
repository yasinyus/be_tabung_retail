<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Header Volume Tabung --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
                📊 Informasi Status Tabung
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">ID Status:</label>
                    <p class="text-base text-gray-900 dark:text-white font-mono bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded-md">
                        #{{ $record->id }}
                    </p>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Tanggal Pengecekan:</label>
                    <p class="text-base text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded-md font-mono">
                        📅 {{ $record->tanggal->format('d/m/Y') }}
                    </p>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Lokasi:</label>
                    <p class="text-base text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded-md">
                        📍 {{ $record->nama_lokasi }}
                    </p>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Nama Petugas:</label>
                    <p class="text-base text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded-md">
                        👤 {{ $record->nama }}
                    </p>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Jumlah Tabung:</label>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-3 py-2 rounded-md border border-green-200 dark:border-green-800">
                        {{ count($tabungList) }} unit
                    </p>
                </div>
                
                @if($record->keterangan)
                <div class="space-y-2 md:col-span-2 xl:col-span-3">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Keterangan:</label>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                            📝 {{ $record->keterangan }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        {{-- Daftar Tabung --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-b border-gray-200 dark:border-gray-700">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    🏷️ Daftar Tabung
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                        {{ count($tabungList) }} item
                    </span>
                </h4>
            </div>
            
            @if(count($tabungList) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-r border-gray-200 dark:border-gray-700">
                                    No.
                                </th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-r border-gray-200 dark:border-gray-700">
                                    QR Code / ID Tabung
                                </th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Status Tabung
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($tabungList as $tabung)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                    <td class="px-8 py-5 whitespace-nowrap border-r border-gray-200 dark:border-gray-700">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 text-sm font-bold">
                                            {{ $tabung['no'] }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap border-r border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center space-x-3">
                                            <span class="text-2xl">🏷️</span>
                                            <span class="text-base font-mono font-semibold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded-lg">
                                                {{ $tabung['qr_code'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap">
                                        @if($tabung['status'] === 'isi')
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                                ✅ Isi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                                                ❌ Kosong
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-8 py-12 text-center">
                    <div class="text-6xl mb-4">📦</div>
                    <p class="text-lg text-gray-500 dark:text-gray-400 font-medium">Tidak ada data tabung untuk volume ini</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Silakan tambahkan data tabung terlebih dahulu</p>
                </div>
            @endif
        </div>
        
        {{-- Statistik Status --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8">
            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-3 flex items-center gap-3">
                📈 Statistik Status
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Total Tabung Berisi:</label>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-4 py-3 rounded-lg border border-green-200 dark:border-green-700">
                        🟢 {{ collect($tabungList)->where('status', 'isi')->count() }} unit
                    </p>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Total Tabung Kosong:</label>
                    <p class="text-lg font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-3 rounded-lg border border-red-200 dark:border-red-700">
                        🔴 {{ collect($tabungList)->where('status', 'kosong')->count() }} unit
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Informasi Timestamp --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8">
            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-3 flex items-center gap-3">
                ⏰ Informasi Waktu
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Data Dibuat:</label>
                    <p class="text-base text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-4 py-3 rounded-lg font-mono border border-gray-200 dark:border-gray-700">
                        🕐 {{ $record->created_at->format('d/m/Y H:i:s') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
