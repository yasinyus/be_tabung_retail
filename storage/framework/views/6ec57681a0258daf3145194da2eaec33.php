<div class="p-6">
    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-700">
        <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100 mb-2">
            📋 History Data Tabung: <?php echo e($kodeTabung); ?>

        </h3>
        <p class="text-blue-700 dark:text-blue-300 text-sm">
            Riwayat audit
        </p>
    </div>

    <!--[if BLOCK]><![endif]--><?php if($history->count() > 0): ?>
        <div class="overflow-x-auto">
            <table style="width:100%">
                <thead style="width:100%">
                    <tr>
                        <td style="width:2%">
                            #
                        </td>
                        <td style="width:20%">
                            Tanggal Audit
                        </td>
                        <td style="width:20%">
                            Lokasi
                        </td>
                        <td style="width:15%">
                            Auditor
                        </td>
                        <td style="width:15%">
                            Status
                        </td>
                        <td style="width:15%">
                            Keterangan
                        </td>

                        <td style="width:15%">
                            Terakhir Audit
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            // Parse tabung data to get status for this specific tabung
                            $tabungData = $audit->tabung;
                            $tabungList = [];
                            $currentTabungStatus = '-';
                            $totalTabung = 0;
                            
                            if (is_array($tabungData)) {
                                $tabungList = $tabungData;
                            } elseif (is_string($tabungData)) {
                                $decoded = json_decode($tabungData, true);
                                if (is_array($decoded)) {
                                    $tabungList = $decoded;
                                }
                            }
                            
                            $totalTabung = count($tabungList);
                            
                            // Find status for current tabung
                            foreach ($tabungList as $tabungItem) {
                                if (isset($tabungItem['qr_code']) && $tabungItem['qr_code'] === $kodeTabung) {
                                    $currentTabungStatus = $tabungItem['status'] ?? '-';
                                    break;
                                }
                            }
                            
                            // Resolve location name
                            $lokasi = $audit->lokasi;
                            $lokasiNama = $lokasi;
                            if (str_starts_with($lokasi, 'GD')) {
                                $gudang = \App\Models\Gudang::where('kode_gudang', $lokasi)->first();
                                $lokasiNama = $gudang ? $gudang->nama_gudang : $lokasi;
                            } elseif (str_starts_with($lokasi, 'PA') || str_starts_with($lokasi, 'PU')) {
                                $pelanggan = \App\Models\Pelanggan::where('kode_pelanggan', $lokasi)->first();
                                $lokasiNama = $pelanggan ? $pelanggan->nama_pelanggan : $lokasi;
                            }
                        ?>
                        
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full text-xs font-medium">
                                    #<?php echo e($index + 1); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <div class="font-medium"><?php echo e(\Carbon\Carbon::parse($audit->tanggal)->format('d/m/Y')); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <div class="font-medium"><?php echo e($lokasiNama); ?>  (<?php echo e($lokasi); ?>)</div>
                                
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <div class="flex items-center">
                                    <span class="text-gray-400 mr-1">👤</span>
                                    <?php echo e($audit->nama ?: 'Tidak diketahui'); ?>

                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <!--[if BLOCK]><![endif]--><?php if($currentTabungStatus === 'Isi'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        ✅ <?php echo e($currentTabungStatus); ?>

                                    </span>
                                <?php elseif($currentTabungStatus === 'Kosong'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        ❌ <?php echo e($currentTabungStatus); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                        ⚪ <?php echo e($currentTabungStatus); ?>

                                    </span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                <!--[if BLOCK]><![endif]--><?php if($audit->keterangan): ?>
                                    <div class="max-w-xs truncate" title="<?php echo e($audit->keterangan); ?>">
                                        <?php echo e($audit->keterangan); ?>

                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Tidak ada keterangan</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?php echo e(\Carbon\Carbon::parse($audit->tanggal)->diffForHumans()); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Total <?php echo e($history->count()); ?> riwayat audit untuk tabung <?php echo e($kodeTabung); ?>

            </p>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">📋</div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                Belum Ada Riwayat Audit
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                Tabung <?php echo e($kodeTabung); ?> belum pernah diaudit
            </p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\resources\views/filament/components/tabung-history.blade.php ENDPATH**/ ?>