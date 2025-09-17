<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📊 Detail Stok per Gudang
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($gudangStats as $gudang)
                @php
                    $persentase = $gudang->total_tabung > 0 ? round(($gudang->tabung_isi / $gudang->total_tabung) * 100, 1) : 0;
                @endphp
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        {{ $gudang->nama_gudang }}
                                    </dt>
                                    <dd class="text-xs text-gray-400 dark:text-gray-500">
                                        Kode: {{ $gudang->kode_gudang }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        
                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <div class="text-center p-2 bg-blue-50 dark:bg-blue-900/20 rounded">
                                <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $gudang->total_tabung }}</div>
                                <div class="text-xs text-blue-500 dark:text-blue-300">Total</div>
                            </div>
                            <div class="text-center p-2 bg-green-50 dark:bg-green-900/20 rounded">
                                <div class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $gudang->tabung_isi }}</div>
                                <div class="text-xs text-green-500 dark:text-green-300">Isi</div>
                            </div>
                            <div class="text-center p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                                <div class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{ $gudang->tabung_kosong }}</div>
                                <div class="text-xs text-yellow-500 dark:text-yellow-300">Kosong</div>
                            </div>
                            <div class="text-center p-2 bg-purple-50 dark:bg-purple-900/20 rounded">
                                <div class="text-lg font-semibold text-purple-600 dark:text-purple-400">{{ number_format((float)$gudang->total_volume, 1) }}L</div>
                                <div class="text-xs text-purple-500 dark:text-purple-300">Volume</div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Tingkat Isi</span>
                                <span class="font-medium 
                                    @if($persentase >= 80) text-green-600 dark:text-green-400
                                    @elseif($persentase >= 50) text-yellow-600 dark:text-yellow-400
                                    @else text-red-600 dark:text-red-400
                                    @endif">
                                    {{ $persentase }}%
                                </span>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full 
                                    @if($persentase >= 80) bg-green-500
                                    @elseif($persentase >= 50) bg-yellow-500
                                    @else bg-red-500
                                    @endif" 
                                    style="width: {{ $persentase }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-8">
                    Tidak ada data gudang
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
