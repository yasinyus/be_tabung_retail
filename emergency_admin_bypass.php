<?php

echo "=== EMERGENCY ADMIN BYPASS ===\n\n";

// Create completely independent admin interface
echo "Creating emergency admin interface...\n";

$emergencyAdminHtml = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Admin - TabungRetail</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .header { background: #f59e0b; color: white; padding: 15px; margin: -20px -20px 20px -20px; }
        .success { background: #10b981; color: white; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { background: #ef4444; color: white; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .btn { background: #3b82f6; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f9fafb; }
        .actions { display: flex; gap: 5px; }
        .btn-sm { padding: 4px 8px; font-size: 12px; }
        .btn-red { background: #ef4444; }
        .btn-green { background: #10b981; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚨 Emergency Admin Interface - TabungRetail</h1>
            <p>Temporary admin interface while fixing main Filament admin</p>
        </div>';

// Bootstrap Laravel for data access
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    $emergencyAdminHtml .= '<div class="success">✅ Laravel successfully loaded</div>';
    
    // Get users data
    $users = \App\Models\User::all();
    $emergencyAdminHtml .= '<div class="success">✅ Database connected - Found ' . $users->count() . ' users</div>';
    
    $emergencyAdminHtml .= '
        <h2>👥 Users Management</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Panel Access</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($users as $user) {
        $canAccess = $user->canAccessPanel(null) ? '✅ YES' : '❌ NO';
        $emergencyAdminHtml .= "
                <tr>
                    <td>{$user->id}</td>
                    <td>{$user->name}</td>
                    <td>{$user->email}</td>
                    <td><strong>{$user->role}</strong></td>
                    <td>{$user->created_at->format('Y-m-d H:i')}</td>
                    <td>{$canAccess}</td>
                </tr>";
    }
    
    $emergencyAdminHtml .= '</tbody></table>';
    
    // Get other resources
    try {
        $tabungCount = \App\Models\Tabung::count();
        $armadaCount = \App\Models\Armada::count();
        $pelangganCount = \App\Models\Pelanggan::count();
        $gudangCount = \App\Models\Gudang::count();
        
        $emergencyAdminHtml .= '
        <h2>📊 Resource Summary</h2>
        <table>
            <tr><td><strong>Tabung Gas</strong></td><td>' . $tabungCount . ' records</td></tr>
            <tr><td><strong>Armada Kendaraan</strong></td><td>' . $armadaCount . ' records</td></tr>
            <tr><td><strong>Pelanggan</strong></td><td>' . $pelangganCount . ' records</td></tr>
            <tr><td><strong>Gudang</strong></td><td>' . $gudangCount . ' records</td></tr>
        </table>';
        
    } catch (Exception $e) {
        $emergencyAdminHtml .= '<div class="error">⚠️ Some models not accessible: ' . $e->getMessage() . '</div>';
    }
    
} catch (Exception $e) {
    $emergencyAdminHtml .= '<div class="error">❌ Laravel failed to load: ' . $e->getMessage() . '</div>';
    $emergencyAdminHtml .= '<p>This indicates a fundamental Laravel issue that needs to be fixed first.</p>';
}

$emergencyAdminHtml .= '
        <h2>🔧 Troubleshooting Actions</h2>
        <div style="margin: 20px 0;">
            <h3>If you can see this page but Filament admin still shows 403:</h3>
            <ol>
                <li><strong>Web Server Issue:</strong> Try accessing <code>http://your-domain/index.php/admin</code></li>
                <li><strong>URL Rewriting:</strong> Check if mod_rewrite (Apache) or try_files (Nginx) is configured</li>
                <li><strong>File Permissions:</strong> Ensure web server can read Laravel files</li>
                <li><strong>Built-in Server Test:</strong> Run <code>php artisan serve --host=0.0.0.0 --port=8080</code></li>
            </ol>
            
            <h3>Commands to run on server:</h3>
            <pre style="background: #f9fafb; padding: 15px; border-radius: 4px;">
composer dump-autoload --optimize
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan optimize:clear
            </pre>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background: #fef3c7; border-radius: 4px;">
            <h3>🎯 Next Steps:</h3>
            <p>This emergency interface proves Laravel is working. The 403 issue is likely:</p>
            <ul>
                <li>Web server configuration (Apache/Nginx)</li>
                <li>URL rewriting rules</li>
                <li>File/directory permissions</li>
                <li>Server-level security restrictions</li>
            </ul>
        </div>
    </div>
</body>
</html>';

// Write emergency admin to public directory
file_put_contents('public/emergency_admin.php', $emergencyAdminHtml);

echo "✅ Emergency admin interface created!\n";
echo "🌐 Access: http://8.215.70.68/emergency_admin.php\n";
echo "\nThis will help determine if the issue is:\n";
echo "1. Laravel/PHP (if emergency admin doesn't work)\n";
echo "2. Filament/routing (if emergency admin works but main admin doesn't)\n";
echo "3. Web server config (if index.php URLs work but clean URLs don't)\n";

?>
