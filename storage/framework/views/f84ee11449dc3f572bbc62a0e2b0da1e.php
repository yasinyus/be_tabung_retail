<div x-data="{
    currentPage: 1,
    itemsPerPage: 10,
    totalItems: <?php echo e($activities ? $activities->count() : 0); ?>,
    get totalPages() {
        return Math.ceil(this.totalItems / this.itemsPerPage);
    },
    get pageInfo() {
        return `Halaman ${this.currentPage} dari ${this.totalPages}`;
    },
    get paginationInfo() {
        const startIndex = (this.currentPage - 1) * this.itemsPerPage + 1;
        const endIndex = Math.min(this.currentPage * this.itemsPerPage, this.totalItems);
        return `Menampilkan ${startIndex}-${endIndex} dari ${this.totalItems} aktivitas`;
    },
    shouldShowRow(index) {
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        return index >= startIndex && index < endIndex;
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
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Riwayat Aktivitas Tabung</h3>
        <!--[if BLOCK]><![endif]--><?php if($activities && $activities->count() > 5): ?>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Total: <?php echo e($activities->count()); ?> aktivitas
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
    
    <?php if($activities && $activities->count() > 0): ?>
        <div class="overflow-x-auto">
            <table style="100%">
                <thead >
                    <tr>
                        <td style="width: 5%">
                            No
                        </td>
                        <td style="width: 12%">
                            Tanggal
                        </td>
                        <td style="width: 25%">
                            Nama Aktivitas
                        </td>
                        <td style="width: 15%">
                            Dari
                        </td>
                        <td style="width: 15%">
                            Tujuan
                        </td>
                        <td style="width: 20%">
                            Keterangan
                        </td>
                        <td style="width: 13%">
                            Petugas
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr x-show="shouldShowRow(<?php echo e($index); ?>)">
                            <td style="width: 5%" class="text-center">
                                <span x-text="((currentPage - 1) * itemsPerPage) + <?php echo e($index + 1); ?>"></span>
                            </td>
                            <td style="width: 12%">
                                <?php echo e($activity->tanggal ?? ($activity->created_at ? $activity->created_at->format('d/m/Y') : '-')); ?>

                            </td>
                            <td style="width: 25%">
                                <?php echo e($activity->nama_aktivitas ?? '-'); ?>

                            </td>
                            <td style="width: 15%">
                                <?php echo e($activity->dari ?? '-'); ?>

                            </td>
                            <td style="width: 15%">
                                <?php echo e($activity->tujuan ?? '-'); ?>

                            </td>
                           <td style="width: 20%">
                                <?php echo e($activity->keterangan ?? '-'); ?>

                            </td>
                            <td style="width: 13%">
                                <?php echo e($activity->nama_petugas ?? '-'); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
        
        <div x-show="totalItems > itemsPerPage" class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400" x-text="paginationInfo"></div>
            <div class="flex space-x-2">
                <button 
                    type="button"
                    @click.stop="prevPage()" 
                    :disabled="currentPage === 1"
                    class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    ← Sebelumnya
                </button>
                <span class="px-3 py-1 text-sm text-gray-500 dark:text-gray-400" x-text="pageInfo"></span>
                <button 
                    type="button"
                    @click.stop="nextPage()" 
                    :disabled="currentPage === totalPages"
                    class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Selanjutnya →
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-8">
            <div class="text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada aktivitas</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat aktivitas untuk tabung ini.</p>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\resources\views/filament/components/tabung-activity-table.blade.php ENDPATH**/ ?>