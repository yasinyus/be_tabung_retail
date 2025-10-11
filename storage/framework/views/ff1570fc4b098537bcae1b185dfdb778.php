<div class="space-y-6">
    <!-- Info Pelanggan -->
    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Informasi Pelanggan</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Kode:</span>
                <span class="text-gray-900 dark:text-white"><?php echo e($pelanggan->kode_pelanggan); ?></span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Nama:</span>
                <span class="text-gray-900 dark:text-white"><?php echo e($pelanggan->nama_pelanggan); ?></span>
            </div>
        </div>
    </div>

    <br><br>

    <!-- Dual Table Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- History Deposit -->
        <div class="space-y-3" 
             x-data="{
                currentPageDeposit: 1,
                itemsPerPageDeposit: 10,
                get totalPagesDeposit() {
                    return Math.ceil(<?php echo e($deposits->count()); ?> / this.itemsPerPageDeposit);
                },
                get startIndexDeposit() {
                    return (this.currentPageDeposit - 1) * this.itemsPerPageDeposit;
                },
                get endIndexDeposit() {
                    return Math.min(this.startIndexDeposit + this.itemsPerPageDeposit, <?php echo e($deposits->count()); ?>);
                },
                goToPageDeposit(page) {
                    if (page >= 1 && page <= this.totalPagesDeposit) {
                        this.currentPageDeposit = page;
                    }
                },
                nextPageDeposit() {
                    if (this.currentPageDeposit < this.totalPagesDeposit) {
                        this.currentPageDeposit++;
                    }
                },
                prevPageDeposit() {
                    if (this.currentPageDeposit > 1) {
                        this.currentPageDeposit--;
                    }
                }
             }">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">History Deposit</h3>
            
            <!--[if BLOCK]><![endif]--><?php if($deposits && $deposits->count() > 0): ?>
                <div class="overflow-hidden bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-300 dark:border-gray-600">
                    <div class="overflow-x-auto">
                        <table style="width:100%">
                            <thead>
                                <tr>
                                    <td style="width:15%" class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        No
                                    </td>
                                    <td style="width:30%" class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tanggal
                                    </td>
                                    <td style="width:25%" class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Jumlah
                                    </td>
                                    <td style="width:30%" class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Keterangan
                                    </td>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900">
                                <?php $depositIndex = 0; ?>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $deposits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deposit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800" 
                                        x-show="Math.floor(<?php echo e($depositIndex); ?> / itemsPerPageDeposit) + 1 === currentPageDeposit">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                            <span x-text="startIndexDeposit + (<?php echo e($depositIndex); ?> % itemsPerPageDeposit) + 1"></span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                            <?php echo e($deposit->tanggal ? $deposit->tanggal->format('d/m/Y') : ($deposit->created_at ? $deposit->created_at->format('d/m/Y') : '-')); ?>

                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400 border border-gray-300 dark:border-gray-600">
                                            Rp <?php echo e(number_format($deposit->saldo, 0, ',', '.')); ?>

                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                            <?php echo e($deposit->keterangan ?: '-'); ?>

                                        </td>
                                    </tr>
                                    <?php $depositIndex++; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls for Deposits -->
                    <div class="bg-white dark:bg-gray-900 px-4 py-3 border-t border-gray-300 dark:border-gray-600 sm:px-6">
                        <div class="flex items-center justify-between">
                   
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Showing
                                        <span class="font-medium" x-text="startIndexDeposit + 1"></span>
                                        to
                                        <span class="font-medium" x-text="endIndexDeposit"></span>
                                        of
                                        <span class="font-medium"><?php echo e($deposits->count()); ?></span>
                                        results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <button @click.stop="prevPageDeposit()" 
                                                :disabled="currentPageDeposit === 1"
                                                :class="currentPageDeposit === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-700'"
                                                class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Sebelumnya
                                        </button>
                                        
                                        <template x-for="page in Array.from({length: totalPagesDeposit}, (_, i) => i + 1)" :key="page">
                                            <button @click.stop="goToPageDeposit(page)" 
                                                    :class="page === currentPageDeposit ? 'z-10 bg-indigo-50 dark:bg-indigo-900 border-indigo-500 text-indigo-600 dark:text-indigo-300' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                                    x-text="page">
                                            </button>
                                        </template>
                                        
                                        <button @click.stop="nextPageDeposit()" 
                                                :disabled="currentPageDeposit === totalPagesDeposit"
                                                :class="currentPageDeposit === totalPagesDeposit ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-700'"
                                                class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Berikutnya
                                        </button>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-8 bg-white dark:bg-gray-900 rounded-lg shadow">
                    <div class="text-gray-500 dark:text-gray-400">
                        <p class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Belum ada deposit</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat deposit untuk pelanggan ini.</p>
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <br><br>

        <!-- History Transaksi -->
        <div class="space-y-3" 
             x-data="{
                currentPage: 1,
                itemsPerPage: 10,
                get totalPages() {
                    return Math.ceil(<?php echo e($transactions->count()); ?> / this.itemsPerPage);
                },
                get startIndex() {
                    return (this.currentPage - 1) * this.itemsPerPage;
                },
                get endIndex() {
                    return Math.min(this.startIndex + this.itemsPerPage, <?php echo e($transactions->count()); ?>);
                },
                goToPage(page) {
                    if (page >= 1 && page <= this.totalPages) {
                        this.currentPage = page;
                    }
                },
                nextPage() {
                    if (this.currentPage < this.totalPages) {
                        this.currentPage++;
                    }
                },
                prevPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                    }
                }
             }">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">History Transaksi</h3>
            
            <!--[if BLOCK]><![endif]--><?php if($transactions && $transactions->count() > 0): ?>
                <div class="overflow-hidden bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-300 dark:border-gray-600">
                    <div class="overflow-x-auto">
                        <table style="width:100%">
                            <thead>
                                <tr>
                                    <td style="width:10%" class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        No
                                    </td>
                                    <td style="width:20%" class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tanggal
                                    </td>
                                    <td style="width:15%">
                                        Jml Tabung
                                    </td>
                                    <td style="width:25%">
                                        Tabung
                                    </td>
                                    <td style="width:15%">
                                        Total
                                    </td>
                                    <td style="width:15%">
                                        Status
                                    </td>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900">
                                <?php $transactionIndex = 0; ?>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800" 
                                        x-show="Math.floor(<?php echo e($transactionIndex); ?> / itemsPerPage) + 1 === currentPage">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                            <span x-text="startIndex + (<?php echo e($transactionIndex); ?> % itemsPerPage) + 1"></span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                            <?php echo e($transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') : ($transaction->created_at ? $transaction->created_at->format('d/m/Y') : '-')); ?>

                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                            <?php echo e($transaction->jumlah_tabung); ?> 
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                                            <!--[if BLOCK]><![endif]--><?php if($transaction->detailTransaksi && $transaction->detailTransaksi->tabung): ?>
                                                <?php
                                                    $tabungData = $transaction->detailTransaksi->tabung;
                                                    $tabungList = [];
                                                    foreach($tabungData as $item) {
                                                        if(isset($item['kode_tabung']) && isset($item['volume'])) {
                                                            $tabungList[] = $item['kode_tabung'] . ' (' . $item['volume'] . ' m3)';
                                                        }
                                                    }
                                                ?>
                                                <div class="max-w-xs">
                                                    <!--[if BLOCK]><![endif]--><?php if(count($tabungList) > 0): ?>
                                                        <div class="text-xs space-y-1">
                                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = array_slice($tabungList, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabung): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 px-2 py-1 rounded text-xs">
                                                                    <?php echo e($tabung); ?>

                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if(count($tabungList) > 3): ?>
                                                                <div class="text-gray-500 text-xs">
                                                                    +<?php echo e(count($tabungList) - 3); ?> lainnya
                                                                </div>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-gray-400 text-xs">-</span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-xs">-</span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-red-600 dark:text-red-400 border border-gray-300 dark:border-gray-600">
                                            Rp <?php echo e(number_format($transaction->total ?? 0, 0, ',', '.')); ?>

                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                                            <?php
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
                                            ?>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo e($statusColors[$transaction->status] ?? 'bg-gray-100 text-gray-800'); ?>">
                                                <?php echo e($statusLabels[$transaction->status] ?? ucfirst($transaction->status)); ?>

                                            </span>
                                        </td>
                                    </tr>
                                    <?php $transactionIndex++; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="bg-white dark:bg-gray-900 px-4 py-3 border-t border-gray-300 dark:border-gray-600 sm:px-6">
                        <div class="flex items-center justify-between">
                      
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Showing
                                        <span class="font-medium" x-text="startIndex + 1"></span>
                                        to
                                        <span class="font-medium" x-text="endIndex"></span>
                                        of
                                        <span class="font-medium"><?php echo e($transactions->count()); ?></span>
                                        results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <button @click.stop="prevPage()" 
                                                :disabled="currentPage === 1"
                                                :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-700'"
                                                class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Sebelumnya
                                        </button>
                                        
                                        <template x-for="page in Array.from({length: totalPages}, (_, i) => i + 1)" :key="page">
                                            <button @click.stop="goToPage(page)" 
                                                    :class="page === currentPage ? 'z-10 bg-indigo-50 dark:bg-indigo-900 border-indigo-500 text-indigo-600 dark:text-indigo-300' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                                    x-text="page">
                                            </button>
                                        </template>
                                        
                                        <button @click.stop="nextPage()" 
                                                :disabled="currentPage === totalPages"
                                                :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-700'"
                                                class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Berikutnya
                                        </button>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-8 bg-white dark:bg-gray-900 rounded-lg shadow">
                    <div class="text-gray-500 dark:text-gray-400">
                        <p class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Belum ada transaksi</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat transaksi untuk pelanggan ini.</p>
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    



    
</div>
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\resources\views/filament/components/pelanggan-history.blade.php ENDPATH**/ ?>