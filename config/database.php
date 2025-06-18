<?php
// Database configuration for Railway MySQL
// Parse the MYSQL_PUBLIC_URL to get connection details

$mysql_url = getenv('MYSQL_PUBLIC_URL');

if ($mysql_url) {
    // Parse the MySQL URL format: mysql://user:password@host:port/database
    $url_parts = parse_url($mysql_url);
    
    $host = $url_parts['host'];
    $port = $url_parts['port'] ?? 3306;
    $username = $url_parts['user'];
    $password = $url_parts['pass'];
    $database = ltrim($url_parts['path'], '/'); // Remove leading slash
    
    error_log("Using MYSQL_PUBLIC_URL - Host: $host, Port: $port, Database: $database, User: $username");
} else {
    // Fallback to individual environment variables
    $host = getenv('MYSQLHOST') ?: 'localhost';
    $port = getenv('MYSQLPORT') ?: '3306';
    $database = getenv('MYSQLDATABASE') ?: 'railway';
    $username = getenv('MYSQLUSER') ?: 'root';
    $password = getenv('MYSQLPASSWORD') ?: '';
    
    error_log("Using individual env vars - Host: $host, Port: $port, Database: $database, User: $username");
}

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::ATTR_TIMEOUT => 30,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Test connection
    $pdo->query("SELECT 1");
    error_log("Database connection successful to: $host:$port/$database");
    
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    error_log("Connection details - Host: $host, Port: $port, Database: $database, User: $username");
    
    // Return JSON error for API endpoints
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'error' => 'Database connection failed',
            'message' => 'Unable to connect to Railway MySQL database.',
            'debug' => [
                'host' => $host,
                'port' => $port,
                'database' => $database,
                'error' => $e->getMessage()
            ]
        ]);
        exit;
    }
    
    die("Database connection failed: " . $e->getMessage());
}

// Start session for admin functionality
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
