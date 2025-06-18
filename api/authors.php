<?php
require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Cache-Control, Pragma, Expires');

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get single author
                $stmt = $pdo->prepare("SELECT id, username, nama, email, created_at FROM penulis WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $author = $stmt->fetch();
                
                if ($author) {
                    echo json_encode($author);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Author not found']);
                }
            } else {
                // Get all authors
                $stmt = $pdo->query("SELECT id, username, nama, email, created_at FROM penulis ORDER BY nama");
                $authors = $stmt->fetchAll();
                echo json_encode($authors);
            }
            break;
            
        case 'POST':
            // Create new author (admin only)
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Hash password
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO penulis (username, password, nama, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $input['username'],
                $hashedPassword,
                $input['nama'],
                $input['email']
            ]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
            
        case 'PUT':
            // Update author (admin only)
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!empty($input['password'])) {
                // Update with new password
                $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE penulis SET username = ?, password = ?, nama = ?, email = ? WHERE id = ?");
                $stmt->execute([
                    $input['username'],
                    $hashedPassword,
                    $input['nama'],
                    $input['email'],
                    $input['id']
                ]);
            } else {
                // Update without changing password
                $stmt = $pdo->prepare("UPDATE penulis SET username = ?, nama = ?, email = ? WHERE id = ?");
                $stmt->execute([
                    $input['username'],
                    $input['nama'],
                    $input['email'],
                    $input['id']
                ]);
            }
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            // Delete author (admin only)
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            
            $id = $_GET['id'];
            
            // Check if author has articles
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM artikel WHERE penulis_id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetch()['count'];
            
            if ($count > 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Cannot delete author with articles']);
                break;
            }
            
            $stmt = $pdo->prepare("DELETE FROM penulis WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
