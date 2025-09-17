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
    <div class="space-y-6">
        <!-- Header dengan informasi -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Monitor Download QR Codes
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Pantau progress download QR codes yang sedang berjalan atau sudah selesai
                    </p>
                </div>
                <div>
                    <a href="<?php echo e(route('filament.admin.resources.tabungs.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Tabung
                    </a>
                </div>
            </div>
        </div>

        <!-- Info cards -->
        <?php
            $userId = Auth::id();
            $totalDownloads = $userId ? \App\Models\DownloadLog::where('user_id', $userId)->where('type', 'qr_codes')->count() : 0;
            $pendingDownloads = $userId ? \App\Models\DownloadLog::where('user_id', $userId)->where('type', 'qr_codes')->where('status', 'pending')->count() : 0;
            $processingDownloads = $userId ? \App\Models\DownloadLog::where('user_id', $userId)->where('type', 'qr_codes')->where('status', 'processing')->count() : 0;
            $completedDownloads = $userId ? \App\Models\DownloadLog::where('user_id', $userId)->where('type', 'qr_codes')->where('status', 'completed')->count() : 0;
        ?>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total</p>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100"><?php echo e($totalDownloads); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pending</p>
                        <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100"><?php echo e($pendingDownloads); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Processing</p>
                        <p class="text-2xl font-bold text-orange-900 dark:text-orange-100"><?php echo e($processingDownloads); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Completed</p>
                        <p class="text-2xl font-bold text-green-900 dark:text-green-100"><?php echo e($completedDownloads); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <?php echo e($this->table); ?>

        </div>

        <!--[if BLOCK]><![endif]--><?php if($totalDownloads === 0): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum ada download</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Belum ada proses download QR codes yang dimulai.</p>
            <a href="<?php echo e(route('filament.admin.resources.tabungs.index')); ?>" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                Mulai Download QR Codes
            </a>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <script>
        // Auto refresh setiap 5 detik jika ada download yang sedang berjalan
        <!--[if BLOCK]><![endif]--><?php if($pendingDownloads > 0 || $processingDownloads > 0): ?>
        setTimeout(function() {
            window.location.reload();
        }, 5000);
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </script>
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
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\resources\views/filament/resources/tabungs/pages/download-progress.blade.php ENDPATH**/ ?>