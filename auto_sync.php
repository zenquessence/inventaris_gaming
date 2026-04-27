<?php
/**
 * Auto-sync script for GitHub repository
 * Place this file in your web root and call it via webhook or cron job
 */

// Security: Only allow access from localhost or with secret key
$allowed_ips = ['127.0.0.1', '::1'];
$secret_key = $_GET['secret'] ?? '';

if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips) && $secret_key !== 'your-secret-key-here') {
    http_response_code(403);
    die('Access denied');
}

// Repository configuration
$repo_url = 'https://github.com/zenquessence/inventaris_gaming.git';
$target_dir = __DIR__;
$branch = 'main';

// Headers for JSON response
header('Content-Type: application/json');

try {
    // Change to target directory
    chdir($target_dir);
    
    // Pull latest changes
    $output = [];
    $return_code = 0;
    
    exec("git pull origin $branch 2>&1", $output, $return_code);
    
    if ($return_code !== 0) {
        throw new Exception('Git pull failed: ' . implode("\n", $output));
    }
    
    // Clear PHP OPcache if available
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    // Clear any session files that might be stale
    $session_path = session_save_path();
    if ($session_path && is_dir($session_path)) {
        $files = glob($session_path . 'sess_*');
        foreach ($files as $file) {
            if (is_file($file) && (time() - filemtime($file)) > 3600) {
                unlink($file);
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Repository synced successfully',
        'timestamp' => date('Y-m-d H:i:s'),
        'git_output' => $output
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
