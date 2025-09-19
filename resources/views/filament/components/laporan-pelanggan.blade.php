<div class="space-y-6">
    <!-- Header Info -->
    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Laporan Pelanggan</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Kode:</span>
                <span class="text-gray-900 dark:text-white">{{ $pelanggan->kode_pelanggan }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Nama:</span>
                <span class="text-gray-900 dark:text-white">{{ $pelanggan->nama_pelanggan }}</span>
            </div>
        </div>
    </div>

    <!-- Laporan Pelanggan Table -->
    <div class="space-y-3">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tabel Laporan</h3>
        
        @if($laporanData && $laporanData->count() > 0)
            <div class="overflow-hidden bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-300 dark:border-gray-600">
                <div class="overflow-x-auto">
                    <table style="width: 100%" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <td style="width:10%" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border border-gray-300 dark:border-gray-600">
                                    No
                                </td>
                                <td style="width:10%" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border border-gray-300 dark:border-gray-600">
                                    Tanggal
                                </td>
                                <td style="width:10%" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border border-gray-300 dark:border-gray-600">
                                    Keterangan
                                </td>
                                <td style="width:10%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border border-gray-300 dark:border-gray-600">
                                    Jumlah Tabung
                                </td>
                                <td style="width:10%" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border border-gray-300 dark:border-gray-600">
                                    Harga
                                </td>
                                <td style="width:10%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border border-gray-300 dark:border-gray-600">
                                    Deposit +
                                </td>
                                <td style="width:10%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border border-gray-300 dark:border-gray-600">
                                    Deposit -
                                </td>
                                <td style="width:10%" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border border-gray-300 dark:border-gray-600">
                                    Sisa
                                </td>
                                <td style="width:10%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border border-gray-300 dark:border-gray-600">
                                    Konfirmasi
                                </td>
                                <td style="width:10%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border border-gray-300 dark:border-gray-600">
                                    Export
                                </td>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($laporanData as $index => $laporan)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                        {{ $laporan->tanggal->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                        {{ $laporan->keterangan }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                        {{ $laporan->tabung ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                        {{ $laporan->formatted_harga }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                        {{ number_format($laporan->tambahan_deposit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                        {{ number_format($laporan->pengurangan_deposit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                        {{ $laporan->formatted_sisa_deposit }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center border border-gray-300 dark:border-gray-600">
                                        @if($laporan->konfirmasi)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                ✓
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                                -
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                            PDF
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-8 bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-300 dark:border-gray-600">
                <div class="text-gray-500 dark:text-gray-400">
                    <p class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Belum ada laporan</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada data laporan untuk pelanggan ini.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->

</div>
