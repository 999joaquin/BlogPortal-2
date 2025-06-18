<?php
// Test database connection
header('Content-Type: application/json');

try {
    require_once 'config/database.php';
    
    // Test basic connection
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    
    // Get database info
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $dbInfo = $stmt->fetch();
    
    // Get table counts
    $tables = [];
    foreach (['artikel', 'kategori', 'penulis'] as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $tables[$table] = $stmt->fetch()['count'];
        } catch (Exception $e) {
            $tables[$table] = 'Table not found';
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Database connection successful',
        'database' => $dbInfo['db_name'],
        'php_version' => PHP_VERSION,
        'tables' => $tables,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>
