<?php
// Simple Railway Backend Status Page - Minimal version for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a timeout for database operations
set_time_limit(30);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Railway Backend - Blog Indonesia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status { padding: 10px; border-radius: 4px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .btn { display: inline-block; padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚂 Railway Backend Status</h1>
        
        <div class="status success">
            ✅ <strong>Apache is running!</strong> PHP <?php echo PHP_VERSION; ?>
        </div>
        
        <div class="status info">
            📊 <strong>Server Info:</strong><br>
            Server Time: <?php echo date('Y-m-d H:i:s T'); ?><br>
            Port: <?php echo $_ENV['PORT'] ?? getenv('PORT') ?? '80'; ?><br>
            Request URI: <?php echo $_SERVER['REQUEST_URI'] ?? 'N/A'; ?>
        </div>

        <h3>🔧 Environment Variables</h3>
        <div class="status info">
            <pre><?php
            $env_vars = [
                'PORT' => getenv('PORT') ?: 'Not set',
                'MYSQL_PUBLIC_URL' => getenv('MYSQL_PUBLIC_URL') ? 'Set (' . strlen(getenv('MYSQL_PUBLIC_URL')) . ' chars)' : 'Not set',
                'MYSQLHOST' => getenv('MYSQLHOST') ?: 'Not set',
                'MYSQLPORT' => getenv('MYSQLPORT') ?: 'Not set',
                'MYSQLDATABASE' => getenv('MYSQLDATABASE') ?: 'Not set',
                'MYSQLUSER' => getenv('MYSQLUSER') ?: 'Not set',
                'MYSQLPASSWORD' => getenv('MYSQLPASSWORD') ? 'Set (' . strlen(getenv('MYSQLPASSWORD')) . ' chars)' : 'Not set'
            ];
            
            foreach ($env_vars as $key => $value) {
                echo "$key: $value\n";
            }
            ?></pre>
        </div>

        <h3>🗄️ Database Connection Test</h3>
        <?php
        try {
            // Set shorter timeout for database connection
            ini_set('mysql.connect_timeout', 10);
            ini_set('default_socket_timeout', 10);
            
            if (file_exists('config/database.php')) {
                echo '<div class="status info">📁 Database config file found</div>';
                
                // Capture any output from database.php
                ob_start();
                $start_time = microtime(true);
                
                require_once 'config/database.php';
                
                $load_time = round((microtime(true) - $start_time) * 1000, 2);
                $output = ob_get_clean();
                
                if ($output) {
                    echo '<div class="status warning">⚠️ Database config output:<pre>' . htmlspecialchars($output) . '</pre></div>';
                }
                
                echo '<div class="status info">⏱️ Config loaded in ' . $load_time . 'ms</div>';
                
                if (isset($pdo)) {
                    // Test basic connection
                    $stmt = $pdo->query("SELECT 1 as test, NOW() as server_time");
                    $result = $stmt->fetch();
                    
                    echo '<div class="status success">✅ Database connection successful!</div>';
                    echo '<div class="status info">🕐 Database server time: ' . $result['server_time'] . '</div>';
                    
                    // Test tables
                    $tables_status = [];
                    foreach (['artikel', 'kategori', 'penulis'] as $table) {
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                            $count = $stmt->fetch()['count'];
                            $tables_status[] = "$table: $count records";
                        } catch (Exception $e) {
                            $tables_status[] = "$table: Error - " . $e->getMessage();
                        }
                    }
                    
                    echo '<div class="status info">📊 Tables:<br>' . implode('<br>', $tables_status) . '</div>';
                    
                } else {
                    echo '<div class="status error">❌ PDO object not created</div>';
                }
                
            } else {
                echo '<div class="status error">❌ Database config file not found</div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="status error">❌ Database Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<div class="status info">🔍 Error details:<br>';
            echo 'File: ' . $e->getFile() . '<br>';
            echo 'Line: ' . $e->getLine() . '<br>';
            echo 'Code: ' . $e->getCode() . '</div>';
        }
        ?>

        <h3>🔗 Quick Tests</h3>
        <div>
            <a href="test-connection.php" class="btn" target="_blank">🧪 Detailed DB Test</a>
            <a href="api/categories.php" class="btn" target="_blank">📂 Categories API</a>
            <a href="api/articles.php" class="btn" target="_blank">📄 Articles API</a>
            <a href="admin/login.html" class="btn" target="_blank">🔐 Admin Panel</a>
        </div>

        <h3>📋 File Structure Check</h3>
        <div class="status info">
            <pre><?php
            $important_files = [
                'config/database.php',
                'api/articles.php',
                'api/categories.php',
                'admin/login.html',
                '.htaccess'
            ];
            
            foreach ($important_files as $file) {
                $exists = file_exists($file);
                $size = $exists ? filesize($file) : 0;
                echo ($exists ? '✅' : '❌') . " $file" . ($exists ? " ($size bytes)" : '') . "\n";
            }
            ?></pre>
        </div>

        <div class="status info">
            <small>🆔 Request ID: <?php echo uniqid(); ?> | Generated: <?php echo date('Y-m-d H:i:s'); ?></small>
        </div>
    </div>
</body>
</html>
