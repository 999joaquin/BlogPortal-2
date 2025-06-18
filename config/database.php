<?php
// Railway Database Configuration - Simplified with better error handling
error_reporting(E_ALL);

// Set connection timeout
ini_set('mysql.connect_timeout', 15);
ini_set('default_socket_timeout', 15);

$mysql_url = getenv('MYSQL_PUBLIC_URL');

if ($mysql_url) {
    // Parse the MySQL URL format: mysql://user:password@host:port/database
    $url_parts = parse_url($mysql_url);
    
    if (!$url_parts) {
        throw new Exception("Invalid MYSQL_PUBLIC_URL format");
    }
    
    $host = $url_parts['host'] ?? null;
    $port = $url_parts['port'] ?? 3306;
    $username = $url_parts['user'] ?? null;
    $password = $url_parts['pass'] ?? null;
    $database = isset($url_parts['path']) ? ltrim($url_parts['path'], '/') : null;
    
    if (!$host || !$username || !$database) {
        throw new Exception("Missing required database connection parameters in MYSQL_PUBLIC_URL");
    }
    
    error_log("Railway DB Config - Host: $host, Port: $port, Database: $database, User: $username");
} else {
    // Fallback to individual environment variables
    $host = getenv('MYSQLHOST');
    $port = getenv('MYSQLPORT') ?: '3306';
    $database = getenv('MYSQLDATABASE');
    $username = getenv('MYSQLUSER');
    $password = getenv('MYSQLPASSWORD');
    
    if (!$host || !$username || !$database) {
        throw new Exception("Missing required database environment variables. Please set MYSQL_PUBLIC_URL or individual MYSQL* variables.");
    }
    
    error_log("Individual DB Config - Host: $host, Port: $port, Database: $database, User: $username");
}

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::ATTR_TIMEOUT => 15, // 15 second timeout
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        PDO::MYSQL_ATTR_COMPRESS => true
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Test connection with a simple query
    $pdo->query("SELECT 1");
    error_log("✅ Database connection successful to: $host:$port/$database");
    
} catch(PDOException $e) {
    $error_msg = "Database connection failed: " . $e->getMessage();
    error_log("❌ " . $error_msg);
    error_log("Connection details - Host: $host, Port: $port, Database: $database, User: $username");
    
    // For API endpoints, return JSON error
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
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]
        ]);
        exit;
    }
    
    // For web pages, throw exception to be caught by calling script
    throw new Exception($error_msg);
}

// Start session for admin functionality
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
