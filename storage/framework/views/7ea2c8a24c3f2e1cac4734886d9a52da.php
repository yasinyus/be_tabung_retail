<div class="space-y-4">
    <!-- Header -->
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Data Refund Tabung
        </h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Kode Pelanggan: <span class="font-semibold"><?php echo e($kode_pelanggan); ?></span> <br> 
            Nama: <span class="font-semibold"><?php echo e($nama_pelanggan); ?></span>
        </p>
        <br>
        <br>
    </div>

    <!--[if BLOCK]><![endif]--><?php if($refund_data->count() > 0): ?>
        <!-- Table -->
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <table style="width: 100%">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            BAST ID
                        </td>
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </td>
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Jumlah Tabung
                        </td>
                        
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tanggal Refund
                        </td>
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </td>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $refund_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $refund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                <?php echo e($refund->bast_id); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    <?php if($refund->status === 'Rusak'): ?> bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    <?php elseif($refund->status === 'Hilang'): ?> bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    <?php else: ?> bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    <?php endif; ?>">
                                    <?php echo e($refund->status); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <?php echo e($refund->jumlah_tabung); ?> tabung
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <?php echo e($refund->created_at ? $refund->created_at->format('d/m/Y H:i') : '-'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?php echo e(route('filament.admin.resources.refunds.create', ['bast_id' => $refund->bast_id])); ?>" 
                                   target="_blank"
                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <br>
        <br>
        
        <!-- Summary -->
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Total: <?php echo e($refund_data->count()); ?> record refund dengan status "Rusak"
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Total Tabung: <?php echo e($refund_data->sum('jumlah_tabung')); ?> tabung
                </span>
            </div>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-8">
      
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                Tidak Ada Data Refund
            </h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Belum ada data refund tabung dengan status "Rusak" untuk pelanggan ini.
            </p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\resources\views/filament/components/refund-table.blade.php ENDPATH**/ ?>