<?php
require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get single category
                $stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $category = $stmt->fetch();
                
                if ($category) {
                    echo json_encode($category);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Category not found']);
                }
            } else {
                // Get all categories with article count
                $stmt = $pdo->query("
                    SELECT k.*, 
                           (SELECT COUNT(*) FROM artikel a WHERE a.kategori_id = k.id AND a.status = 'published') as article_count 
                    FROM kategori k 
                    ORDER BY k.nama
                ");
                $categories = $stmt->fetchAll();
                
                echo json_encode($categories);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
