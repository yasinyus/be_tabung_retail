<x-filament-widgets::widget>
    <div class="grid gap-6">
        <!-- Overall Statistics Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold mb-2">📊 Statistik Tabung Gas</h3>
                    <p class="text-blue-100 text-sm">Ringkasan status tabung di seluruh gudang</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">{{ number_format($overall['total_tabung']) }}</div>
                    <div class="text-blue-200 text-sm">Total Tabung</div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-white/10 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-300">{{ number_format($overall['total_isi']) }}</div>
                    <div class="text-blue-200 text-sm">Tabung Isi</div>
                </div>
                <div class="bg-white/10 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-300">{{ number_format($overall['total_kosong']) }}</div>
                    <div class="text-blue-200 text-sm">Tabung Kosong</div>
                </div>
                <div class="bg-white/10 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-cyan-300">{{ number_format($overall['total_volume'], 1) }}L</div>
                    <div class="text-blue-200 text-sm">Total Volume</div>
                </div>
                <div class="bg-white/10 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-purple-300">{{ $overall['persentase_isi'] }}%</div>
                    <div class="text-blue-200 text-sm">Tingkat Isi</div>
                </div>
            </div>
        </div>

        <!-- Gudang Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($gudangStats as $gudang)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <!-- Gudang Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $gudang->nama_gudang }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $gudang->kode_gudang }}</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H7m2 0v-6a2 2 0 012-2h2a2 2 0 012 2v6"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Statistics Grid -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Total Tabung -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                            <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($gudang->total_tabung) }}
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-300">Total</div>
                        </div>

                        <!-- Tabung Isi -->
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                            <div class="text-xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($gudang->tabung_isi) }}
                            </div>
                            <div class="text-xs text-green-600 dark:text-green-400">Isi</div>
                        </div>

                        <!-- Tabung Kosong -->
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 text-center">
                            <div class="text-xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ number_format($gudang->tabung_kosong) }}
                            </div>
                            <div class="text-xs text-yellow-600 dark:text-yellow-400">Kosong</div>
                        </div>

                        <!-- Volume -->
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3 text-center">
                            <div class="text-xl font-bold text-purple-600 dark:text-purple-400">
                                {{ number_format($gudang->total_volume, 1) }}L
                            </div>
                            <div class="text-xs text-purple-600 dark:text-purple-400">Volume</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    @if($gudang->total_tabung > 0)
                        @php
                            $persentaseIsi = round(($gudang->tabung_isi / $gudang->total_tabung) * 100, 1);
                        @endphp
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                                <span>Tingkat Isi</span>
                                <span>{{ $persentaseIsi }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $persentaseIsi }}%"></div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Action -->
                    <div class="mt-4">
                        <a href="/admin/volume-tabungs/gudang/{{ $gudang->kode_gudang }}" 
                           class="inline-flex items-center text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H7m2 0v-6a2 2 0 012-2h2a2 2 0 012 2v6"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum Ada Data Gudang</h3>
                        <p class="text-gray-500 dark:text-gray-400">Silakan tambahkan data gudang dan stok tabung terlebih dahulu.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-widgets::widget>
