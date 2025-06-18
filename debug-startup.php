<?php
// Debug startup issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Railway Debug Info ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current Time: " . date('Y-m-d H:i:s') . "\n";
echo "Port: " . (getenv('PORT') ?: 'Not set') . "\n";
echo "Working Directory: " . getcwd() . "\n";

echo "\n=== Environment Variables ===\n";
$env_vars = ['PORT', 'MYSQL_PUBLIC_URL', 'MYSQLHOST', 'MYSQLPORT', 'MYSQLDATABASE', 'MYSQLUSER', 'MYSQLPASSWORD'];
foreach ($env_vars as $var) {
    $value = getenv($var);
    echo "$var: " . ($value ? (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) : 'Not set') . "\n";
}

echo "\n=== File Check ===\n";
$files = ['config/database.php', 'api/articles.php', '.htaccess', 'index.php'];
foreach ($files as $file) {
    echo "$file: " . (file_exists($file) ? 'EXISTS' : 'MISSING') . "\n";
}

echo "\n=== Apache Status ===\n";
echo "Apache modules loaded: " . (function_exists('apache_get_modules') ? 'YES' : 'NO') . "\n";

echo "\n=== Database Test ===\n";
try {
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        echo "Database config loaded: YES\n";
        if (isset($pdo)) {
            $pdo->query("SELECT 1");
            echo "Database connection: SUCCESS\n";
        } else {
            echo "Database connection: PDO not created\n";
        }
    } else {
        echo "Database config file: MISSING\n";
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\n=== End Debug ===\n";
?>
