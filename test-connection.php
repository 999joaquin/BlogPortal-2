<?php
// Comprehensive connection test
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Environment check
    $env_check = [
        'MYSQL_PUBLIC_URL' => getenv('MYSQL_PUBLIC_URL') ? 'Set' : 'Not set',
        'MYSQLHOST' => getenv('MYSQLHOST') ? 'Set' : 'Not set',
        'MYSQLPORT' => getenv('MYSQLPORT') ? 'Set' : 'Not set',
        'MYSQLDATABASE' => getenv('MYSQLDATABASE') ? 'Set' : 'Not set',
        'MYSQLUSER' => getenv('MYSQLUSER') ? 'Set' : 'Not set',
        'MYSQLPASSWORD' => getenv('MYSQLPASSWORD') ? 'Set' : 'Not set'
    ];
    
    require_once 'config/database.php';
    
    // Test basic connection
    $stmt = $pdo->query("SELECT 1 as test, NOW() as current_time");
    $result = $stmt->fetch();
    
    // Get database info
    $stmt = $pdo->query("SELECT DATABASE() as db_name, VERSION() as mysql_version");
    $dbInfo = $stmt->fetch();
    
    // Get table counts
    $tables = [];
    foreach (['artikel', 'kategori', 'penulis'] as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $tables[$table] = $stmt->fetch()['count'];
        } catch (Exception $e) {
            $tables[$table] = 'Table not found: ' . $e->getMessage();
        }
    }
    
    // Test published articles specifically
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM artikel WHERE status = 'published'");
        $publishedCount = $stmt->fetch()['count'];
    } catch (Exception $e) {
        $publishedCount = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Database connection successful',
        'environment' => $env_check,
        'database' => [
            'name' => $dbInfo['db_name'],
            'mysql_version' => $dbInfo['mysql_version'],
            'server_time' => $result['current_time']
        ],
        'tables' => $tables,
        'published_articles' => $publishedCount,
        'php_version' => PHP_VERSION,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed',
        'environment' => $env_check ?? [],
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>
