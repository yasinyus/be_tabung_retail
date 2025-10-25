<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-8">
        
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
                📋 Informasi Aktivitas Tabung
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          

                <div class="space-y-2 ">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-500">Aktivitas Tabung:</label>
                    <p class="text-base font-semibold bg-blue-50 dark:bg-blue-900/20 px-3 py-2 rounded-md text-blue-800 dark:text-blue-200">
                        <?php echo e($record->nama_aktivitas); ?>

                    </p>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Petugas:</label>
                    <p class="text-base text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded-md">
                        <?php echo e($record->nama_petugas); ?>

                    </p>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Lokasi Asal:</label>
                    <p class="text-base text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded-md">
                        <?php echo e($record->dari); ?>

                    </p>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Lokasi Tujuan:</label>
                    <p class="text-base text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded-md">
                        <?php echo e($record->tujuan); ?>

                    </p>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Status Tabung:</label>
                    <div class="bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded-md">
                        <!--[if BLOCK]><![endif]--><?php if($record->status === 'Isi'): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                                ✅ <?php echo e($record->status); ?>

                            </span>
                        <?php elseif($record->status === 'Kosong'): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200">
                                ❌ <?php echo e($record->status); ?>

                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                ⏳ <?php echo e($record->status); ?>

                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Total Tabung:</label>
                    <p class="text-xl font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-3 py-2 rounded-md border border-blue-200 dark:border-blue-800">
                        <?php echo e($record->total_tabung ?? 0); ?> unit
                    </p>
                </div>
                
                <!--[if BLOCK]><![endif]--><?php if($showVolumeHarga): ?>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Total Volume:</label>
                        <p class="text-xl font-bold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 px-3 py-2 rounded-md border border-purple-200 dark:border-purple-800">
                            <?php echo e(number_format($totalVolume, 2, ',', '.')); ?> m³
                        </p>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Total Harga:</label>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-3 py-2 rounded-md border border-green-200 dark:border-green-800">
                            Rp <?php echo e(number_format($totalHarga, 0, ',', '.')); ?>

                        </p>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Tanggal Aktivitas:</label>
                    <p class="text-base text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded-md font-mono">
                        📅 <?php echo e($record->tanggal); ?>

                    </p>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">User Input:</label>
                    <p class="text-base text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded-md">
                        👤 <?php echo e($record->user->name ?? 'N/A'); ?>

                    </p>
                </div>
            </div>
            
            <!--[if BLOCK]><![endif]--><?php if($record->keterangan): ?>
                <div class="mt-8 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">📝 Keterangan:</label>
                    <p class="text-base text-amber-800 dark:text-amber-200 leading-relaxed">
                        <?php echo e($record->keterangan); ?>

                    </p>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        
        
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-b border-gray-200 dark:border-gray-700">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    🏷️ Daftar Tabung
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                        <?php echo e(count($tabungList)); ?> item
                    </span>
                </h4>
            </div>
            
            <!--[if BLOCK]><![endif]--><?php if(count($tabungList) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-r border-gray-200 dark:border-gray-700">
                                    No.
                                </th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-r border-gray-200 dark:border-gray-700">
                                    QR Code / ID Tabung
                                </th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Status Tabung
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tabungList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabung): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                    <td class="px-8 py-5 whitespace-nowrap border-r border-gray-200 dark:border-gray-700">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 text-sm font-bold">
                                            <?php echo e($tabung['no']); ?>

                                        </span>
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap border-r border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center space-x-3">
                                            <span class="text-2xl">🏷️</span>
                                            <span class="text-base font-mono font-semibold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded-lg">
                                                <?php echo e($tabung['qr_code']); ?>

                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap">
                                        <!--[if BLOCK]><![endif]--><?php if($tabung['status'] === 'Isi'): ?>
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                                ✅ <?php echo e($tabung['status']); ?>

                                            </span>
                                        <?php elseif($tabung['status'] === 'Kosong'): ?>
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                                                ❌ <?php echo e($tabung['status']); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                ⏳ <?php echo e($tabung['status']); ?>

                                            </span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="px-8 py-12 text-center">
                    <div class="text-6xl mb-4">📦</div>
                    <p class="text-lg text-gray-500 dark:text-gray-400 font-medium">Tidak ada data tabung untuk aktivitas ini</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Silakan tambahkan data tabung terlebih dahulu</p>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        
        
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8">
            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-3 flex items-center gap-3">
                ⏰ Informasi Waktu
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400">Waktu Input Data:</label>
                    <p class="text-base text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-4 py-3 rounded-lg font-mono border border-gray-200 dark:border-gray-700">
                        🕐 <?php echo e($record->waktu ? \Carbon\Carbon::parse($record->waktu)->format('d/m/Y H:i:s') : 'Belum ada data waktu'); ?>

                    </p>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\resources\views/filament/resources/tabung-activity-resource/pages/view-tabung-activity.blade.php ENDPATH**/ ?>