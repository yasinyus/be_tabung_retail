<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo e($pelanggan->nama_pelanggan); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 10px;
            font-size: 11px;
            color: #666;
        }
        
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .invoice-info > div {
            width: 48%;
        }
        
        .invoice-info h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .invoice-info p {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .invoice-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .invoice-details h3 {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #333;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px dashed #ddd;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
        }
        
        .detail-label {
            font-weight: 500;
        }
        
        .detail-value {
            text-align: right;
        }
        
        .tabung-list {
            margin-bottom: 30px;
        }
        
        .tabung-list h3 {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .tabung-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .tabung-table th,
        .tabung-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .tabung-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        
        .tabung-table td {
            font-size: 10px;
        }
        
        .tabung-table .text-center {
            text-align: center;
        }
        
        .tabung-table .text-right {
            text-align: right;
        }
        
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 3px 0;
        }
        
        .summary-row.total {
            border-top: 1px solid #333;
            padding-top: 10px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 13px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status.confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status.pending {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <strong>PT CEI</strong><br>
        </div>
        <h1>INVOICE</h1>
        
    </div>

    <!-- Invoice Info -->
    <div class="invoice-info">
        <div>
            <h3>Data Pelanggan</h3>
            <p><strong>Nama:</strong> <?php echo e($pelanggan->nama_pelanggan); ?></p>
            <p><strong>Kode:</strong> <?php echo e($pelanggan->kode_pelanggan); ?></p>
            <?php if($pelanggan->alamat): ?>
                <p><strong>Alamat:</strong> <?php echo e($pelanggan->alamat); ?></p>
            <?php endif; ?>
            <?php if($pelanggan->telepon): ?>
                <p><strong>Telepon:</strong> <?php echo e($pelanggan->telepon); ?></p>
            <?php endif; ?>
            <?php if($pelanggan->jenis_pelanggan): ?>
                <p><strong>Jenis:</strong> <?php echo e(ucfirst($pelanggan->jenis_pelanggan)); ?></p>
            <?php endif; ?>
        </div>
        
        <div>
            <h3>Info Invoice</h3>
            <p><strong>Tanggal:</strong> <?php echo e($laporan->tanggal->format('d/m/Y')); ?></p>
            <p><strong>Dicetak:</strong> <?php echo e(now()->format('d/m/Y H:i')); ?></p>
            <p><strong>Status:</strong> 
                <span class="status <?php echo e($laporan->konfirmasi ? 'confirmed' : 'pending'); ?>">
                    <?php echo e($laporan->konfirmasi ? 'DIKONFIRMASI' : 'PENDING'); ?>

                </span>
            </p>
        </div>
    </div>

     <!-- List Tabung -->
    <?php if($listTabung && $listTabung->count() > 0): ?>
        <div class="tabung-list">
            <h3>Daftar Tabung Terjual</h3>
            <table class="tabung-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Tabung</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php $totalTabung = 0; ?>
                    <?php $__currentLoopData = $listTabung; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tabung): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $harga = $tabung->harga_jual ?? 0; $totalTabung += $harga; ?>
                        <tr>
                            <td class="text-center"><?php echo e($index + 1); ?></td>
                            <td><?php echo e($tabung->kode_tabung); ?></td>
                            
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            
            <!-- Summary Tabung -->
            <div class="summary">
                <div class="summary-row">
                    <span>Total Tabung:</span>
                    <span><?php echo e($listTabung->count()); ?> unit</span>
                </div>
                <div class="summary-row total">
                    <span>Total Penjualan Tabung:</span>
                    <span>Rp <?php echo e(number_format($totalTabung, 0, ',', '.')); ?></span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Invoice Details -->
    <div class="invoice-details">
        <h3>Detail Transaksi</h3>
        <div class="detail-row">
            <span class="detail-label">Keterangan:</span>
            <span class="detail-value"><?php echo e($laporan->keterangan); ?></span>
        </div>
        <?php if($laporan->tabung): ?>
            <div class="detail-row">
                <span class="detail-label">Jumlah Tabung:</span>
                <span class="detail-value"><?php echo e($laporan->tabung); ?> unit</span>
            </div>
        <?php endif; ?>
        <?php if($laporan->harga): ?>
            <div class="detail-row">
                <span class="detail-label">Harga Transaksi:</span>
                <span class="detail-value">Rp <?php echo e(number_format($laporan->harga, 0, ',', '.')); ?></span>
            </div>
        <?php endif; ?>
        <?php if($laporan->tambahan_deposit): ?>
            <div class="detail-row">
                <span class="detail-label">Tambahan Deposit:</span>
                <span class="detail-value" style="color: green;">+Rp <?php echo e(number_format($laporan->tambahan_deposit, 0, ',', '.')); ?></span>
            </div>
        <?php endif; ?>
        <?php if($laporan->pengurangan_deposit): ?>
            <div class="detail-row">
                <span class="detail-label">Pengurangan Deposit:</span>
                <span class="detail-value" style="color: red;">-Rp <?php echo e(number_format($laporan->pengurangan_deposit, 0, ',', '.')); ?></span>
            </div>
        <?php endif; ?>
        <div class="detail-row">
            <span class="detail-label">Sisa Deposit:</span>
            <span class="detail-value"><strong>Rp <?php echo e(number_format($laporan->sisa_deposit, 0, ',', '.')); ?></strong></span>
        </div>
    </div>

   

    <!-- Footer -->
    <div class="footer">
        <p><em>Invoice ini digenerate secara otomatis pada <?php echo e(now()->format('d/m/Y H:i:s')); ?></em></p>
        <p>Terima kasih atas kepercayaan Anda menggunakan layanan PT CEI</p>
    </div>
</body>
</html>
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\resources\views/pdf/invoice.blade.php ENDPATH**/ ?>