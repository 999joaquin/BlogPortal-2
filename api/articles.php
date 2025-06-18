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
                // Get single article with author info
                $whereClause = "a.id = ?";
                $params = [$_GET['id']];
                
                // For public access, only show published articles (unless admin is logged in)
                if (!isset($_SESSION['user_id']) && !isset($_GET['admin'])) {
                    $whereClause .= " AND a.status = 'published'";
                }
                
                $stmt = $pdo->prepare("
                    SELECT a.*, 
                           p.nama as penulis_nama, 
                           p.username as penulis_username,
                           p.email as penulis_email,
                           k.nama as kategori_nama 
                    FROM artikel a 
                    LEFT JOIN penulis p ON a.penulis_id = p.id 
                    LEFT JOIN kategori k ON a.kategori_id = k.id 
                    WHERE $whereClause
                ");
                $stmt->execute($params);
                $article = $stmt->fetch();
                
                if ($article) {
                    echo json_encode($article);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Article not found']);
                }
            } else {
                // Get articles list with author info
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                $offset = ($page - 1) * $limit;
                $kategori_id = isset($_GET['kategori_id']) ? $_GET['kategori_id'] : null;
                $search = isset($_GET['search']) ? $_GET['search'] : null;
                $status = isset($_GET['status']) ? $_GET['status'] : null;
                $isAdmin = isset($_GET['admin']) && isset($_SESSION['user_id']);
                
                $where_conditions = [];
                $params = [];
                
                // For public access, only show published articles
                if (!$isAdmin) {
                    $where_conditions[] = "a.status = 'published'";
                }
                
                if ($kategori_id) {
                    $where_conditions[] = "a.kategori_id = ?";
                    $params[] = $kategori_id;
                }
                
                if ($search) {
                    $where_conditions[] = "(a.judul LIKE ? OR a.konten LIKE ? OR a.ringkasan LIKE ? OR p.nama LIKE ?)";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                }
                
                $where_clause = empty($where_conditions) ? "1=1" : implode(' AND ', $where_conditions);
                
                $stmt = $pdo->prepare("
                    SELECT a.*, 
                           p.nama as penulis_nama, 
                           p.username as penulis_username,
                           k.nama as kategori_nama 
                    FROM artikel a 
                    INNER JOIN penulis p ON a.penulis_id = p.id 
                    INNER JOIN kategori k ON a.kategori_id = k.id 
                    WHERE $where_clause
                    ORDER BY a.created_at DESC 
                    LIMIT $limit OFFSET $offset
                ");
                
                $stmt->execute($params);
                $articles = $stmt->fetchAll();
                
                // Get total count
                $count_stmt = $pdo->prepare("
                    SELECT COUNT(*) as total 
                    FROM artikel a 
                    INNER JOIN penulis p ON a.penulis_id = p.id 
                    INNER JOIN kategori k ON a.kategori_id = k.id 
                    WHERE $where_clause
                ");
                $count_stmt->execute($params);
                $total = $count_stmt->fetch()['total'];
                
                echo json_encode([
                    'articles' => $articles,
                    'total' => (int)$total,
                    'page' => $page,
                    'limit' => $limit
                ]);
            }
            break;
            
        case 'POST':
            // Create new article (admin only)
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['judul']) || empty($input['konten']) || empty($input['kategori_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                break;
            }
            
            $penulis_id = isset($input['penulis_id']) ? $input['penulis_id'] : $_SESSION['user_id'];
            
            $stmt = $pdo->prepare("
                INSERT INTO artikel (judul, konten, ringkasan, image_url, penulis_id, kategori_id, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $input['judul'],
                $input['konten'],
                $input['ringkasan'] ?? '',
                $input['image_url'] ?? null,
                $penulis_id,
                $input['kategori_id'],
                $input['status'] ?? 'draft'
            ]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
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
