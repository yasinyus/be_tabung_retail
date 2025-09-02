<x-filament-widgets::widget>
    <x-filament::section>
        @if($record)
            <div class="space-y-6">
                {{-- Header Aktivitas --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Detail Aktivitas Tabung
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">ID Aktivitas</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $record->id }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Aktivitas</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $record->nama_aktivitas }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Petugas</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->nama_petugas }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dari</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->dari }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tujuan</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->tujuan }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($record->status === 'Isi') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                @elseif($record->status === 'Kosong') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                @endif">
                                {{ $record->status }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Tabung</label>
                            <p class="mt-1 text-lg font-bold text-blue-600 dark:text-blue-400">{{ $record->total_tabung ?? 0 }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->tanggal }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">User</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->user->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    @if($record->keterangan)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->keterangan }}</p>
                        </div>
                    @endif
                </div>
                
                {{-- Daftar Tabung --}}
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white">
                            Daftar Tabung ({{ count($tabungList) }} item)
                        </h4>
                    </div>
                    
                    @if(count($tabungList) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            No
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            QR Code / ID Tabung
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($tabungList as $index => $tabung)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                                {{ $tabung['qr_code'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($tabung['status'] === 'Isi') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                                    @elseif($tabung['status'] === 'Kosong') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                                    @endif">
                                                    {{ $tabung['status'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-6 py-8 text-center">
                            <p class="text-gray-500 dark:text-gray-400">Tidak ada data tabung untuk aktivitas ini.</p>
                        </div>
                    @endif
                </div>
                
                {{-- Informasi Timestamp --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Informasi Waktu</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Waktu Input</label>
                            <p class="text-gray-900 dark:text-white">{{ $record->waktu ? \Carbon\Carbon::parse($record->waktu)->format('d/m/Y H:i:s') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Dibuat</label>
                            <p class="text-gray-900 dark:text-white">{{ $record->created_at ? $record->created_at->format('d/m/Y H:i:s') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">Data aktivitas tidak ditemukan.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
