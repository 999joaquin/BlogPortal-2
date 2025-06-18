<?php
require_once 'config/database.php';

header('Content-Type: application/json');

try {
    // Check if penulis table exists and has data
    $stmt = $pdo->query("SELECT id, username, nama FROM penulis");
    $users = $stmt->fetchAll();
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT id, username, nama FROM penulis WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    echo json_encode([
        'status' => 'success',
        'total_users' => count($users),
        'users' => $users,
        'admin_exists' => $admin ? true : false,
        'admin_data' => $admin
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage()
    ]);
}
?>
