<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Codes Tabung Gas</title>
    <style>
        @page {
            margin: 20px;
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            font-size: 11px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
        }
        .header p {
            margin: 5px 0;
            font-size: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        
        .qr-cell { 
            border: 2px solid #000; 
            padding: 10px; 
            text-align: center;
            width: 50%;
            vertical-align: top;
            height: 180px;
        }
        
        .qr-code { 
            width: 100px; 
            height: 100px; 
            margin: 0 auto 8px auto; 
            display: block;
        }
        
        .qr-placeholder {
            width: 100px; 
            height: 100px; 
            margin: 0 auto 8px auto; 
            border: 1px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            font-size: 8px;
            color: #666;
        }
        
        .info { 
            font-size: 10px; 
            line-height: 1.3;
        }
        
        .info div { 
            margin: 2px 0; 
        }
        
        .kode-tabung { 
            font-weight: bold; 
            font-size: 12px; 
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>QR Codes Tabung Gas</h2>
        <p>Generated: <?php echo e(date('d/m/Y H:i')); ?> | Total: <?php echo e(count($qrData)); ?> tabung</p>
    </div>

    <table>
        <?php $__currentLoopData = $qrData->chunk(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td class="qr-cell">
                        <?php if($data['has_qr'] && $data['qr_base64']): ?>
                            <img src="<?php echo e($data['qr_base64']); ?>" alt="QR Code" class="qr-code">
                            <div style="font-size: 8px; color: green;">QR: OK</div>
                        <?php else: ?>
                            <div class="qr-placeholder">
                                QR Not Available
                                <?php if(isset($data['error'])): ?>
                                    <br><small><?php echo e(substr($data['error'], 0, 20)); ?></small>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 8px; color: red;">
                                Debug: has_qr=<?php echo e($data['has_qr'] ? 'true' : 'false'); ?>, 
                                qr_exists=<?php echo e(!empty($data['qr_base64']) ? 'true' : 'false'); ?>

                            </div>
                        <?php endif; ?>
                        
                        <div class="info">
                            <div class="kode-tabung">Kode Tabung: <?php echo e($data['tabung']->kode_tabung); ?></div>
                            <div>Seri Tabung: <?php echo e($data['tabung']->seri_tabung); ?></div>
                            <div>Tahun: <?php echo e($data['tabung']->tahun); ?></div>
                        </div>
                    </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                
                <?php if(count($chunk) == 1): ?>
                    <td class="qr-cell" style="border: none;"></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</body>
</html>
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\resources\views/pdf/tabung-qr-codes.blade.php ENDPATH**/ ?>