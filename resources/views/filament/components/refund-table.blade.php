<div class="space-y-4">
    <!-- Header -->
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Data Refund Tabung
        </h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Kode Pelanggan: <span class="font-semibold">{{ $kode_pelanggan }}</span> <br> 
            Nama: <span class="font-semibold">{{ $nama_pelanggan }}</span>
        </p>
        <br>
        <br>
    </div>

    @if($refund_data->count() > 0)
        <!-- Table -->
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <table style="width: 100%">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            BAST ID
                        </td>
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </td>
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Jumlah Tabung
                        </td>
                        {{-- <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Daftar Tabung
                        </td> --}}
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tanggal Refund
                        </td>
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </td>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($refund_data as $refund)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $refund->bast_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($refund->status === 'Rusak') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @elseif($refund->status === 'Hilang') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @endif">
                                    {{ $refund->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $refund->jumlah_tabung }} tabung
                            </td>
                            {{-- <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                <div class="max-w-xs">
                                    @if(is_array($refund->tabung))
                                        <div class="space-y-1">
                                            @foreach(array_slice($refund->tabung, 0, 3) as $tabung)
                                                <div class="inline-block bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">
                                                    @if(is_array($tabung))
                                                        {{ $tabung['kode_tabung'] ?? 'N/A' }}
                                                    @else
                                                        {{ $tabung }}
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if(count($refund->tabung) > 3)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    +{{ count($refund->tabung) - 3 }} lainnya
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">Data tidak tersedia</span>
                                    @endif
                                </div>
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $refund->created_at ? $refund->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('filament.admin.resources.refunds.create', ['bast_id' => $refund->bast_id]) }}" 
                                   target="_blank"
                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <br>
        <br>
        
        <!-- Summary -->
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Total: {{ $refund_data->count() }} record refund dengan status "Rusak"
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Total Tabung: {{ $refund_data->sum('jumlah_tabung') }} tabung
                </span>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-8">
      
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                Tidak Ada Data Refund
            </h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Belum ada data refund tabung dengan status "Rusak" untuk pelanggan ini.
            </p>
        </div>
    @endif
</div>