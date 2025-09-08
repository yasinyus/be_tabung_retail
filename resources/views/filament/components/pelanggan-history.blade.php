<div class="space-y-6">
    <!-- Info Pelanggan -->
    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Informasi Pelanggan</h3>
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

    <br><br>

    <!-- Dual Table Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- History Deposit -->
        <div class="space-y-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">History Deposit</h3>
            
            @if($deposits && $deposits->count() > 0)
                <div class="overflow-hidden bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-300 dark:border-gray-600">
                    <div class="overflow-x-auto">
                        <table style="width:30%">
                            <thead >
                                <tr>
                                    <td style="width:10%">
                                        Tanggal
                                    </td>
                                    <td style="width:20%">
                                        Jumlah
                                    </td>
                                    <td style="width:30%">
                                        Keterangan
                                    </td>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900">
                                @foreach($deposits as $deposit)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                            {{ $deposit->tanggal ? $deposit->tanggal->format('d/m/Y') : ($deposit->created_at ? $deposit->created_at->format('d/m/Y') : '-') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400 border border-gray-300 dark:border-gray-600">
                                            Rp {{ number_format($deposit->saldo, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                            {{ $deposit->keterangan ?: '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center py-8 bg-white dark:bg-gray-900 rounded-lg shadow">
                    <div class="text-gray-500 dark:text-gray-400">
                        <p class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Belum ada deposit</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat deposit untuk pelanggan ini.</p>
                    </div>
                </div>
            @endif
        </div>

        <br><br>

        <!-- History Transaksi -->
        <div class="space-y-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">History Transaksi</h3>
            
            @if($transactions && $transactions->count() > 0)
                <div class="overflow-hidden bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-300 dark:border-gray-600">
                    <div class="overflow-x-auto">
                        <table style="width:40%">
                            <thead >
                                <tr>
                                    <td style="width:10%" class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tanggal
                                    </td>
                                    <td style="width:10%">
                                        Jml Tabung
                                    </td>
                                    <td style="width:10%">
                                        Total
                                    </td>
                                    <td style="width:10%">
                                        Status
                                    </td>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900">
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                            {{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') : ($transaction->created_at ? $transaction->created_at->format('d/m/Y') : '-') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                            {{ $transaction->jumlah_tabung }} 
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-red-600 dark:text-red-400 border border-gray-300 dark:border-gray-600">
                                            Rp {{ number_format($transaction->total ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                                            @php
                                                $statusColors = [
                                                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                                    'canceled' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                                                ];
                                                $statusLabels = [
                                                    'paid' => 'Paid',
                                                    'pending' => 'Pending',
                                                    'canceled' => 'Canceled',
                                                ];
                                            @endphp
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$transaction->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $statusLabels[$transaction->status] ?? ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center py-8 bg-white dark:bg-gray-900 rounded-lg shadow">
                    <div class="text-gray-500 dark:text-gray-400">
                        <p class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Belum ada transaksi</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat transaksi untuk pelanggan ini.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    



    
</div>
