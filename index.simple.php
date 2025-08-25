<?php
define('LARAVEL_START', microtime(true));

// Simple error handling
if (!file_exists(__DIR__.'/vendor/autoload.php')) {
    die('<h1>Error</h1><p>Composer autoload not found. Please run: composer install</p>');
}

require __DIR__.'/vendor/autoload.php';

try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);
    
} catch (Exception $e) {
    echo '<h1>Laravel Error</h1>';
    echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>File: ' . $e->getFile() . ':' . $e->getLine() . '</p>';
    
    // Log error
    if (is_dir('storage/logs')) {
        error_log(date('Y-m-d H:i:s') . ' Laravel Error: ' . $e->getMessage() . "\n", 3, 'storage/logs/error.log');
    }
}
?>
