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
        <!-- Header Info -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Informasi Pelanggan
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Kode Pelanggan:</span>
                    <span class="text-gray-900 dark:text-white ml-2"><?php echo e($this->pelanggan->kode_pelanggan); ?></span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Nama Pelanggan:</span>
                    <span class="text-gray-900 dark:text-white ml-2"><?php echo e($this->pelanggan->nama_pelanggan); ?></span>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($this->pelanggan->alamat): ?>
                <div class="md:col-span-2">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Alamat:</span>
                    <span class="text-gray-900 dark:text-white ml-2"><?php echo e($this->pelanggan->alamat); ?></span>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><?php if($this->pelanggan->telepon): ?>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Telepon:</span>
                    <span class="text-gray-900 dark:text-white ml-2"><?php echo e($this->pelanggan->telepon); ?></span>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><?php if($this->pelanggan->jenis_pelanggan): ?>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Jenis:</span>
                    <span class="text-gray-900 dark:text-white ml-2"><?php echo e(ucfirst($this->pelanggan->jenis_pelanggan)); ?></span>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        <!-- Filament Table -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Tabel Laporan Transaksi
                </h3>
            </div>
            
            <?php echo e($this->table); ?>

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
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\resources\views/filament/resources/tagihans/pages/laporan-pelanggan.blade.php ENDPATH**/ ?>