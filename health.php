<?php
// Simple health check endpoint
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo json_encode([
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'port' => $_ENV['PORT'] ?? getenv('PORT') ?? '80',
    'php_version' => PHP_VERSION
]);
?>
