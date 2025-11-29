<div class="space-y-6">
    {{-- Header Info --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border">
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pengisian</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $record->tanggal ? $record->tanggal->format('d/m/Y') : '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lokasi</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $record->nama_lokasi ?? $record->lokasi }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Petugas</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $record->nama }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $record->status === 'Selesai' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
                            {{ $record->status === 'isi' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
                            {{ $record->status === 'Proses' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
                            {{ $record->status === 'Pending' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : '' }}
                            {{ $record->status === 'kosong' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : '' }}
                        ">
                            {{ ucfirst($record->status) }}
                        </span>
                    </dd>
                </div>
            </div>
            
            @if($record->keterangan)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Keterangan</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $record->keterangan }}</dd>
            </div>
            @endif
        </div>
    </div>

    {{-- Tabung Details --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Daftar Tabung
                </h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                    {{ is_array($record->tabung) ? count($record->tabung) : 0 }} Tabung
                </span>
            </div>
            
            @if(is_array($record->tabung) && count($record->tabung) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    No
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Kode Tabung
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Volume (m³)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($record->tabung as $index => $tabung)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                        {{ $tabung['kode_tabung'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <span class="font-semibold">{{ $tabung['volume'] ?? 0 }}</span> m³
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Summary Volume --}}
                <div class="mt-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Volume:</span>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                {{ collect($record->tabung)->sum('volume') ?? 0 }} m³
                            </span>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                dari {{ count($record->tabung) }} tabung
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada data tabung</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada tabung yang tercatat untuk pengisian ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Timestamp Info --}}
    <div class="bg-gray-50 dark:bg-gray-900 overflow-hidden shadow-sm rounded-lg border">
        <div class="px-6 py-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Informasi Record</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Dibuat</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $record->created_at ? $record->created_at->format('d/m/Y H:i') : '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Terakhir Diperbarui</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $record->updated_at ? $record->updated_at->format('d/m/Y H:i') : '-' }}
                    </dd>
                </div>
            </div>
        </div>
    </div>
</div>