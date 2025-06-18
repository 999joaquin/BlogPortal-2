<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['username']) || empty($input['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Username and password required']);
                break;
            }
            
            // Check credentials
            $stmt = $pdo->prepare("SELECT * FROM penulis WHERE username = ?");
            $stmt->execute([$input['username']]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($input['password'], $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                echo json_encode([
                    'success' => true,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'nama' => $user['nama']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
            }
            break;
            
        case 'GET':
            // Check if user is logged in
            if (isset($_SESSION['user_id'])) {
                echo json_encode([
                    'authenticated' => true,
                    'user_id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username']
                ]);
            } else {
                echo json_encode(['authenticated' => false]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
