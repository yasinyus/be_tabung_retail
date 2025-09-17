<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Daftar Gudang & Statistik Tabung
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Klik "Lihat Detail" untuk melihat daftar lengkap tabung di setiap gudang.
            </p>
            
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
