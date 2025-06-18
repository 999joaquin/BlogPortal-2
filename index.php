<?php
// Railway Backend Status Page
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Indonesia - Railway Backend</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-server me-2"></i>Blog Indonesia - Railway Backend
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>🚂 Backend Status: <span class="badge bg-success">Running</span></h5>
                                <p class="text-muted">Railway deployment successful!</p>
                                
                                <?php
                                try {
                                    require_once 'config/database.php';
                                    echo '<div class="alert alert-success">
                                        <i class="fas fa-database me-2"></i>Database connection successful!
                                    </div>';
                                    
                                    // Test database
                                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM artikel WHERE status = 'published'");
                                    $articleCount = $stmt->fetch()['count'];
                                    
                                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM kategori");
                                    $categoryCount = $stmt->fetch()['count'];
                                    
                                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM penulis");
                                    $authorCount = $stmt->fetch()['count'];
                                    
                                    echo "<div class='row text-center mt-3'>";
                                    echo "<div class='col-4'>";
                                    echo "<div class='card bg-info text-white'>";
                                    echo "<div class='card-body'>";
                                    echo "<h3>$articleCount</h3>";
                                    echo "<small>Published Articles</small>";
                                    echo "</div></div></div>";
                                    
                                    echo "<div class='col-4'>";
                                    echo "<div class='card bg-success text-white'>";
                                    echo "<div class='card-body'>";
                                    echo "<h3>$categoryCount</h3>";
                                    echo "<small>Categories</small>";
                                    echo "</div></div></div>";
                                    
                                    echo "<div class='col-4'>";
                                    echo "<div class='card bg-warning text-white'>";
                                    echo "<div class='card-body'>";
                                    echo "<h3>$authorCount</h3>";
                                    echo "<small>Authors</small>";
                                    echo "</div></div></div>";
                                    echo "</div>";
                                    
                                } catch (Exception $e) {
                                    echo '<div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Database connection failed: ' . htmlspecialchars($e->getMessage()) . '
                                    </div>';
                                    
                                    echo '<div class="alert alert-info">';
                                    echo '<h6>Environment Variables Check:</h6>';
                                    echo '<ul>';
                                    echo '<li>MYSQL_PUBLIC_URL: ' . (getenv('MYSQL_PUBLIC_URL') ? '✅ Set' : '❌ Not set') . '</li>';
                                    echo '<li>MYSQLHOST: ' . (getenv('MYSQLHOST') ? '✅ Set' : '❌ Not set') . '</li>';
                                    echo '<li>PHP Version: ' . PHP_VERSION . '</li>';
                                    echo '</ul>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            
                            <div class="col-md-6">
                                <h6><i class="fas fa-link me-2"></i>API Endpoints:</h6>
                                <div class="list-group">
                                    <a href="api/articles.php" target="_blank" class="list-group-item list-group-item-action">
                                        <i class="fas fa-newspaper me-2"></i>Articles API
                                    </a>
                                    <a href="api/categories.php" target="_blank" class="list-group-item list-group-item-action">
                                        <i class="fas fa-tags me-2"></i>Categories API
                                    </a>
                                    <a href="api/authors.php" target="_blank" class="list-group-item list-group-item-action">
                                        <i class="fas fa-users me-2"></i>Authors API
                                    </a>
                                    <a href="test-connection.php" target="_blank" class="list-group-item list-group-item-action">
                                        <i class="fas fa-stethoscope me-2"></i>Database Test
                                    </a>
                                </div>
                                
                                <div class="mt-3">
                                    <a href="admin/login.html" class="btn btn-primary w-100">
                                        <i class="fas fa-user-shield me-2"></i>Admin Panel
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Railway Backend for Blog Indonesia | 
                                PHP <?php echo PHP_VERSION; ?> | 
                                Server Time: <?php echo date('Y-m-d H:i:s'); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
