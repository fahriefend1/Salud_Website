<?php
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Query stats
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$totalProducts = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
$pendingOrders = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'completed'");
$completedOrders = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM visits");
$totalVisits = $stmt->fetch()['total'];

// Recent orders
$recentOrders = $pdo->query("
    SELECT o.*, p.name as product_name 
    FROM orders o 
    LEFT JOIN products p ON o.product_id = p.id 
    ORDER BY o.created_at DESC 
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SALUD</title>
    <link rel="stylesheet" href="../assets/css/admin.css"> <!-- PAKAI admin.css BARU -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar (KUNING SALUD) -->
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <img src="../assets/images/Logo Salad Puding Hitam.png" alt="SALUD Logo" class="admin-logo-img"
                     onerror="this.style.display='none'">
                <p>Admin Dashboard</p>
            </div>
            
            <ul class="admin-nav">
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Produk</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li><a href="chat.php"><i class="fas fa-comments"></i> Pesan Kontak</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-eye"></i> Lihat Website</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header dengan Profil -->
            <div class="admin-header">
                <h1><i class="fas fa-chart-line"></i> Dashboard</h1>
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <img src="../assets/images/admin-avatar.png" alt="Admin Avatar" 
                             onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['admin_name'] ?? 'Admin') ?>&background=FDC64E&color=1A1A1A'">
                    </div>
                    <div class="admin-info">
                        <h4><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></h4>
                        <p>Administrator SALUD</p>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="admin-stats">
                <div class="admin-card stat-success">
                    <h3><?= $totalProducts ?></h3>
                    <p><i class="fas fa-box"></i> Total Produk</p>
                </div>
                
                <div class="admin-card stat-warning">
                    <h3><?= $pendingOrders ?></h3>
                    <p><i class="fas fa-clock"></i> Pesanan Pending</p>
                </div>
                
                <div class="admin-card stat-danger">
                    <h3><?= $completedOrders ?></h3>
                    <p><i class="fas fa-check-circle"></i> Pesanan Selesai</p>
                </div>
                
                <div class="admin-card stat-info">
                    <h3><?= $totalVisits ?></h3>
                    <p><i class="fas fa-users"></i> Total Kunjungan</p>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="admin-table-container">
                <div style="padding: 25px 25px 0;">
                    <h3 style="margin: 0; color: var(--admin-dark); font-size: 1.3rem;">
                        <i class="fas fa-history"></i> Pesanan Terbaru
                    </h3>
                </div>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>WhatsApp</th>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recentOrders) > 0): ?>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $order['whatsapp']) ?>" 
                                       target="_blank"
                                       style="color: var(--admin-primary); text-decoration: none;">
                                        <?= htmlspecialchars($order['whatsapp']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($order['product_name'] ?? 'Contact Form') ?></td>
                                <td><?= $order['quantity'] ?></td>
                                <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <span class="admin-badge badge-<?= $order['status'] ?>">
                                        <?= $order['status'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 40px; color: var(--admin-gray);">
                                    <i class="fas fa-shopping-cart" style="font-size: 2rem; margin-bottom: 15px; display: block;"></i>
                                    Belum ada pesanan
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div style="padding: 20px 25px; text-align: right; border-top: 1px solid var(--admin-border);">
                    <a href="orders.php" class="admin-btn admin-btn-primary">
                        <i class="fas fa-list"></i> Lihat Semua Pesanan
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="admin-table-container">
                <div style="padding: 25px;">
                    <h3 style="margin: 0 0 20px 0; color: var(--admin-dark); font-size: 1.3rem;">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <a href="product-add.php" class="admin-btn admin-btn-primary" style="justify-content: center;">
                            <i class="fas fa-plus"></i> Tambah Produk
                        </a>
                        
                        <a href="products.php" class="admin-btn admin-btn-secondary" style="justify-content: center;">
                            <i class="fas fa-edit"></i> Kelola Produk
                        </a>
                        
                        <a href="orders.php" class="admin-btn admin-btn-success" style="justify-content: center;">
                            <i class="fas fa-shopping-cart"></i> Kelola Pesanan
                        </a>
                        
                        <a href="../index.php" target="_blank" class="admin-btn admin-btn-danger" style="justify-content: center;">
                            <i class="fas fa-eye"></i> Lihat Website
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>