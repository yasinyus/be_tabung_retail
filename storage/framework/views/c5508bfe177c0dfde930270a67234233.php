<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggan - <?php echo e($pelanggan->nama_pelanggan); ?></title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        
        .header h2 {
            margin: 10px 0 0 0;
            color: #34495e;
            font-size: 18px;
            font-weight: normal;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e9ecef;
            width: 48%;
        }
        
        .info-box h3 {
            margin: 0 0 10px 0;
            color: #495057;
            font-size: 14px;
        }
        
        .info-box p {
            margin: 5px 0;
            font-size: 12px;
        }
        
        .table-container {
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        
        .summary-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .summary-box h4 {
            margin: 0 0 10px 0;
            color: #0066cc;
        }
        
        @media print {
            body { margin: 0; }
            .header { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PELANGGAN</h1>
        <h2><?php echo e($pelanggan->nama_pelanggan); ?></h2>
    </div>

    <div style="display: table; width: 100%; margin-bottom: 30px;">
        <div style="display: table-cell; width: 50%; vertical-align: top;">
            <div class="info-box">
                <h3>Informasi Pelanggan</h3>
                <p><strong>Kode:</strong> <?php echo e($pelanggan->kode_pelanggan); ?></p>
                <p><strong>Nama:</strong> <?php echo e($pelanggan->nama_pelanggan); ?></p>
                <p><strong>Alamat:</strong> <?php echo e($pelanggan->alamat ?? '-'); ?></p>
                <p><strong>Telepon:</strong> <?php echo e($pelanggan->telepon ?? '-'); ?></p>
            </div>
        </div>
        <div style="display: table-cell; width: 50%; vertical-align: top; padding-left: 20px;">
            <div class="info-box">
                <h3>Detail Laporan</h3>
                <p><strong>Total Transaksi:</strong> <?php echo e($laporans->count()); ?></p>
                <p><strong>Tanggal Cetak:</strong> <?php echo e($tanggal_cetak); ?></p>
                <p><strong>Sisa Deposit Terakhir:</strong> 
                    <?php
                        $lastDeposit = $laporans->first();
                    ?>
                    <?php echo e($lastDeposit && $lastDeposit->sisa_deposit ? 'Rp ' . number_format($lastDeposit->sisa_deposit, 0, ',', '.') : 'Rp 0'); ?>

                </p>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 20%;">Keterangan</th>
                    <th style="width: 12%;">ID BAST Invoice</th>
                    <th style="width: 6%;">Tabung</th>
                    <th style="width: 12%;">Harga</th>
                    <th style="width: 12%;">Deposit (+)</th>
                    <th style="width: 12%;">Deposit (-)</th>
                    <th style="width: 10%;">Sisa</th>
                    <th style="width: 6%;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $laporans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $laporan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-center"><?php echo e($index + 1); ?></td>
                        <td class="text-center"><?php echo e($laporan->tanggal ? $laporan->tanggal->format('d/m/Y') : '-'); ?></td>
                        <td><?php echo e($laporan->keterangan ?? '-'); ?></td>
                        <td class="text-center"><?php echo e($laporan->id_bast_invoice ?? '-'); ?></td>
                        <td class="text-center"><?php echo e($laporan->tabung ?? '-'); ?></td>
                        <td class="text-right"><?php echo e($laporan->harga ? 'Rp ' . number_format($laporan->harga, 0, ',', '.') : '-'); ?></td>
                        <td class="text-right text-success">
                            <?php echo e($laporan->tambahan_deposit ? '+Rp ' . number_format($laporan->tambahan_deposit, 0, ',', '.') : '-'); ?>

                        </td>
                        <td class="text-right text-danger">
                            <?php echo e($laporan->pengurangan_deposit ? '-Rp ' . number_format($laporan->pengurangan_deposit, 0, ',', '.') : '-'); ?>

                        </td>
                        <td class="text-right">
                            <strong><?php echo e($laporan->sisa_deposit ? 'Rp ' . number_format($laporan->sisa_deposit, 0, ',', '.') : 'Rp 0'); ?></strong>
                        </td>
                        <td class="text-center">
                            <?php if($laporan->konfirmasi): ?>
                                <span style="color: #28a745; font-weight: bold;">✓</span>
                            <?php else: ?>
                                <span style="color: #dc3545;">✗</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 20px;">
                            <em>Tidak ada data laporan</em>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($laporans->count() > 0): ?>
        <div class="summary-box">
            <h4>Ringkasan</h4>
            <p><strong>Total Transaksi:</strong> <?php echo e($laporans->count()); ?> transaksi</p>
            <p><strong>Transaksi Terkonfirmasi:</strong> <?php echo e($laporans->where('konfirmasi', true)->count()); ?> dari <?php echo e($laporans->count()); ?></p>
            <?php
                $totalHarga = $laporans->sum('harga');
                $totalDepositMasuk = $laporans->sum('tambahan_deposit');
                $totalDepositKeluar = $laporans->sum('pengurangan_deposit');
            ?>
            <p><strong>Total Harga:</strong> <?php echo e($totalHarga ? 'Rp ' . number_format($totalHarga, 0, ',', '.') : 'Rp 0'); ?></p>
            <p><strong>Total Deposit Masuk:</strong> <?php echo e($totalDepositMasuk ? 'Rp ' . number_format($totalDepositMasuk, 0, ',', '.') : 'Rp 0'); ?></p>
            <p><strong>Total Deposit Keluar:</strong> <?php echo e($totalDepositKeluar ? 'Rp ' . number_format($totalDepositKeluar, 0, ',', '.') : 'Rp 0'); ?></p>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>Laporan ini dicetak pada <?php echo e($tanggal_cetak); ?></p>
        <p>© <?php echo e(date('Y')); ?> Sistem Manajemen Tabung Gas</p>
    </div>
</body>
</html><?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\resources\views/pdf/laporan-pelanggan.blade.php ENDPATH**/ ?>