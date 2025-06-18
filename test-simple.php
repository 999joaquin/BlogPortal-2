<?php
// Ultra-simple test page to verify Railway is working
header('Content-Type: text/plain');
echo "✅ Railway Backend is working!\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Port: " . (getenv('PORT') ?: '80') . "\n";

// Test if we can access environment variables
$mysql_url = getenv('MYSQL_PUBLIC_URL');
if ($mysql_url) {
    echo "✅ MYSQL_PUBLIC_URL is set\n";
    $url_parts = parse_url($mysql_url);
    echo "Host: " . ($url_parts['host'] ?? 'N/A') . "\n";
} else {
    echo "❌ MYSQL_PUBLIC_URL not set\n";
}

echo "\nAll environment variables:\n";
foreach ($_ENV as $key => $value) {
    if (strpos($key, 'MYSQL') !== false || $key === 'PORT') {
        echo "$key: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
    }
}
?>
