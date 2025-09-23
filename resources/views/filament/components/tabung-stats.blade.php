<!-- Stats Cards -->
<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-6 mb-6">
    <!-- Total Tabung -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tabung</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['totalTabung']) }}</p>
            </div>
        </div>
    </div>

    <!-- Tabung Isi -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tabung Isi</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['totalIsi']) }}</p>
            </div>
        </div>
    </div>

    <!-- Tabung Kosong -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tabung Kosong</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['totalKosong']) }}</p>
            </div>
        </div>
    </div>


    <!-- Total Gudang -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Gudang</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['totalGudang']) }}</p>
            </div>
        </div>
    </div>

    <!-- Total Pelanggan -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pelanggan</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['totalPelanggan']) }}</p>
            </div>
        </div>
    </div>

    <!-- Total Armada -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Armada</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['totalArmada']) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Detail Table -->
<div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            📋 Detail per Gudang
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table style="width: 100%" class="min-w-full">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <td scope="col"  style="width: 20%" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tdacking-wider">
                        Gudang
                    </td>
                    <td scope="col"  style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tdacking-wider">
                        Kode
                    </td>
                    <td scope="col"  style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tdacking-wider">
                        Jumlah Tabung
                    </td>
                    <td scope="col"  style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tdacking-wider">
                        Jumlah Status Isi
                    </td>
                    <td scope="col"  style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tdacking-wider">
                        Jumlah Status Kosong
                    </td>
                </tr>
            </thead>
            <tbody class="bg-gray-50 dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($gudangStats as $gudang)
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $gudang->nama_gudang }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                {{ $gudang->kode_gudang }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-200 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                                {{ number_format($gudang->total_tabung) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-200 text-green-800 dark:bg-green-800 dark:text-green-200">
                                {{ number_format($gudang->tabung_isi) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-200 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">
                                {{ number_format($gudang->tabung_kosong) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data gudang tersedia
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Detail Table Pelanggan -->
<div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mt-6">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            👥 Detail per Pelanggan
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table style="width: 100%" class="min-w-full">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <td scope="col" style="width: 20%" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Pelanggan
                    </td>
                    <td scope="col" style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Kode
                    </td>
                    <td scope="col" style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Total
                    </td>
                    <td scope="col" style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Isi
                    </td>
                    <td scope="col" style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Kosong
                    </td>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @forelse($pelangganStats as $pelanggan)
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                            {{ $pelanggan->nama_pelanggan }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                {{ $pelanggan->kode_pelanggan }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-200 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                                {{ number_format($pelanggan->total_tabung) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-200 text-green-800 dark:bg-green-800 dark:text-green-200">
                                {{ number_format($pelanggan->tabung_isi) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-200 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">
                                {{ number_format($pelanggan->tabung_kosong) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data pelanggan tersedia
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Detail Table Armada -->
<div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mt-6">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            🚚 Detail per Armada
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table style="width: 100%" class="min-w-full">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <td scope="col" style="width: 20%" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Nopol
                    </td>
                    <td scope="col" style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Kode Kendaraan
                    </td>
                    <td scope="col" style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Total
                    </td>
                    <td scope="col" style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Isi
                    </td>
                    <td scope="col" style="width: 20%" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Kosong
                    </td>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @forelse($armadaStats as $armada)
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                            {{ $armada->nopol }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                {{ $armada->kode_kendaraan }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-200 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                                {{ number_format($armada->total_tabung) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-200 text-green-800 dark:bg-green-800 dark:text-green-200">
                                {{ number_format($armada->tabung_isi) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-200 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">
                                {{ number_format($armada->tabung_kosong) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data armada tersedia
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
