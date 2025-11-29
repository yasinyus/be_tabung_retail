<div class="space-y-6">
    {{-- Header Info --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
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
                            {{ $record->status === 'Proses' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
                            {{ $record->status === 'Pending' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : '' }}
                        ">
                            {{ $record->status }}
                        </span>
                    </dd>
                </div>
            </div>
        </div>
    </div>

    {{-- Keterangan --}}
    @if($record->keterangan)
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Keterangan</h3>
            <div class="mt-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $record->keterangan }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Data Tabung --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                    Daftar Tabung
                </h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                    {{ is_array($record->tabung) ? count($record->tabung) : 0 }} Tabung
                </span>
            </div>
            
            @if(is_array($record->tabung) && count($record->tabung) > 0)
                <div class="overflow-x-auto">
                    <table style="min-width: 55%;">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    No
                                </td>
                                <td scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Kode Tabung
                                </td>
                                <td scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Volume (m3)
                                </td>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($record->tabung as $index => $tabung)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td style="width: 5%">
                                    {{ $index + 1 }}
                                </td>
                                <td style="width: 25%">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                        {{ $tabung['kode_tabung'] ?? '-' }}
                                    </span>
                                </td>
                                <td style="width: 25%">
                                    <span class="font-semibold">{{ $tabung['volume'] ?? 0 }}</span> m³
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Summary Volume --}}
                <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
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
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H6a1 1 0 00-1 1v1" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada data tabung</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada tabung yang tercatat untuk pengisian ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Timeline/Audit --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Informasi Record</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $record->created_at ? $record->created_at->format('d/m/Y H:i') : '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Diperbarui</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $record->updated_at ? $record->updated_at->format('d/m/Y H:i') : '-' }}
                    </dd>
                </div>
            </div>
        </div>
    </div>
</div>