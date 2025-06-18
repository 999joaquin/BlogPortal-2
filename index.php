<?php
// Simple index page for Railway backend
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Indonesia - Backend</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">🚀 Blog Indonesia Backend</h4>
                    </div>
                    <div class="card-body">
                        <h5>Backend Status: <span class="badge bg-success">Running</span></h5>
                        
                        <?php
                        try {
                            require_once 'config/database.php';
                            echo '<div class="alert alert-success">✅ Database connection successful!</div>';
                            
                            // Test database
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM artikel WHERE status = 'published'");
                            $articleCount = $stmt->fetch()['count'];
                            
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM kategori");
                            $categoryCount = $stmt->fetch()['count'];
                            
                            echo "<p><strong>Published Articles:</strong> $articleCount</p>";
                            echo "<p><strong>Categories:</strong> $categoryCount</p>";
                            
                        } catch (Exception $e) {
                            echo '<div class="alert alert-danger">❌ Database connection failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
                        }
                        ?>
                        
                        <hr>
                        
                        <h6>API Endpoints:</h6>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a href="api/articles.php" target="_blank">📄 Articles API</a>
                            </li>
                            <li class="list-group-item">
                                <a href="api/categories.php" target="_blank">📂 Categories API</a>
                            </li>
                            <li class="list-group-item">
                                <a href="api/authors.php" target="_blank">👥 Authors API</a>
                            </li>
                        </ul>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <a href="admin/login.html" class="btn btn-primary">
                                🔐 Admin Panel
                            </a>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                Railway Backend for Blog Indonesia<br>
                                PHP <?php echo PHP_VERSION; ?> | MySQL Connected
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
