<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Info -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Informasi Pelanggan
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Kode Pelanggan:</span>
                    <span class="text-gray-900 dark:text-white ml-2">{{ $this->pelanggan->kode_pelanggan }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Nama Pelanggan:</span>
                    <span class="text-gray-900 dark:text-white ml-2">{{ $this->pelanggan->nama_pelanggan }}</span>
                </div>
                @if($this->pelanggan->alamat)
                <div class="md:col-span-2">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Alamat:</span>
                    <span class="text-gray-900 dark:text-white ml-2">{{ $this->pelanggan->alamat }}</span>
                </div>
                @endif
                @if($this->pelanggan->telepon)
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Telepon:</span>
                    <span class="text-gray-900 dark:text-white ml-2">{{ $this->pelanggan->telepon }}</span>
                </div>
                @endif
                @if($this->pelanggan->jenis_pelanggan)
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Jenis:</span>
                    <span class="text-gray-900 dark:text-white ml-2">{{ ucfirst($this->pelanggan->jenis_pelanggan) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Filament Table -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Tabel Laporan Transaksi
                </h3>
            </div>
            
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
