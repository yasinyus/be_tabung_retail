<?php

// Script untuk fix exception handler di server
// Jalankan: php fix_exception_handler.php

echo "Fixing exception handler in bootstrap/app.php...\n";

$bootstrapFile = 'bootstrap/app.php';
$content = file_get_contents($bootstrapFile);

// Backup original file
file_put_contents($bootstrapFile . '.backup', $content);
echo "✅ Backup created: bootstrap/app.php.backup\n";

// Fix exception handler - remove problematic route references
$fixedContent = str_replace(
    'return redirect()->guest(route(\'filament.admin.auth.login\'));',
    'return redirect()->guest(\'/admin/login\');',
    $content
);

$fixedContent = str_replace(
    'return redirect()->guest(route(\'login\'));',
    'return redirect()->guest(\'/admin/login\');',
    $fixedContent
);

// Write fixed content
file_put_contents($bootstrapFile, $fixedContent);
echo "✅ Exception handler fixed!\n";

// Clear cache
echo "Clearing cache...\n";
system('php artisan cache:clear');
system('php artisan config:clear');
system('php artisan route:clear');
system('php artisan view:clear');
system('php artisan optimize:clear');

echo "✅ Cache cleared!\n";
echo "✅ Fix completed! Test your API now.\n";
