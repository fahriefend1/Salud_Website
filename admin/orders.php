<?php
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Filter periode default (30 hari terakhir)
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// Validasi tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) $startDate = date('Y-m-d', strtotime('-30 days'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) $endDate = date('Y-m-d');

// Build query dengan filter
$where = "WHERE DATE(o.created_at) BETWEEN ? AND ?";
$params = [$startDate, $endDate];

// Search
$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $where .= " AND (o.customer_name LIKE ? OR o.whatsapp LIKE ? OR p.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Status filter
$status = $_GET['status'] ?? '';
if (!empty($status)) {
    $where .= " AND o.status = ?";
    $params[] = $status;
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total
$countQuery = "SELECT COUNT(*) as total FROM orders o 
               LEFT JOIN products p ON o.product_id = p.id 
               $where";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalOrders = $countStmt->fetch()['total'];
$totalPages = ceil($totalOrders / $limit);

// Ambil data
$query = "SELECT o.*, p.name as product_name, p.price as product_price 
          FROM orders o 
          LEFT JOIN products p ON o.product_id = p.id 
          $where 
          ORDER BY o.created_at DESC 
          LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['status'];
    
    $validStatuses = ['pending', 'processed', 'completed'];
    if (in_array($newStatus, $validStatuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);
        
        header("Location: orders.php?success=Status berhasil diupdate&" . http_build_query($_GET));
        exit();
    }
}

$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin SALUD</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        function confirmUpdate(orderId, currentStatus) {
            const select = document.querySelector(`select[name="status_${orderId}"]`);
            const newStatus = select ? select.value : document.querySelector(`select[name="status"]`).value;
            
            if (currentStatus === newStatus) {
                alert('Status belum berubah!');
                return false;
            }
            return confirm(`Update status pesanan #${orderId} dari "${currentStatus}" menjadi "${newStatus}"?`);
        }
    </script>
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <img src="../assets/images/Logo Salad Puding Hitam.png" alt="SALUD Logo" class="admin-logo-img"
                     onerror="this.style.display='none'">
                <p>Kelola Pesanan</p>
            </div>
            
            <ul class="admin-nav">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Produk</a></li>
                <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li><a href="chat.php"><i class="fas fa-comments"></i> Pesan Kontak</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-eye"></i> Lihat Website</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <div class="admin-header">
                <h1><i class="fas fa-shopping-cart"></i> Kelola Pesanan</h1>
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <img src="../assets/images/admin-avatar.png" alt="Admin Avatar" 
                             onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['admin_name'] ?? 'Admin') ?>&background=FDC64E&color=1A1A1A'">
                    </div>
                    <div class="admin-info">
                        <h4><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></h4>
                        <p>Administrator SALUD</p>
                    </div>
                    <!-- <a href="logout.php" class="admin-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a> -->
                </div>
            </div>

            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="admin-alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="admin-filters">
                <form method="GET">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label><i class="far fa-calendar-alt"></i> Tanggal Mulai</label>
                            <input type="date" name="start_date" value="<?= $startDate ?>" max="<?= date('Y-m-d') ?>" class="admin-form-control">
                        </div>
                        <div class="filter-group">
                            <label><i class="far fa-calendar-alt"></i> Tanggal Akhir</label>
                            <input type="date" name="end_date" value="<?= $endDate ?>" max="<?= date('Y-m-d') ?>" class="admin-form-control">
                        </div>
                        <div class="filter-group">
                            <label><i class="fas fa-filter"></i> Status</label>
                            <select name="status" class="admin-form-control">
                                <option value="">Semua Status</option>
                                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="processed" <?= $status === 'processed' ? 'selected' : '' ?>>Diproses</option>
                                <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>
                        <div class="filter-group" style="flex: 0 0 auto;">
                            <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%; min-width: 120px;">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                        <div class="filter-group" style="flex: 0 0 auto;">
                            <a href="orders.php" class="admin-btn admin-btn-secondary" style="width: 100%; min-width: 120px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; align-items: center; margin-top: 15px;">
                        <div style="flex: 1; position: relative;">
                            <input type="text" name="search" 
                                placeholder="Cari nama, WhatsApp, atau produk..." 
                                value="<?= htmlspecialchars($search) ?>"
                                class="admin-form-control"
                                style="padding-left: 40px; width: 100%;">
                            <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                        </div>
                        <button type="submit" class="admin-btn admin-btn-primary" style="white-space: nowrap;">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </form>
                
                <?php if ($search || $status || $startDate != date('Y-m-d', strtotime('-30 days')) || $endDate != date('Y-m-d')): ?>
                <div style="margin-top: 20px; padding: 15px; background: rgba(253, 198, 78, 0.1); border-radius: 8px; border: 1px solid rgba(253, 198, 78, 0.3);">
                    <p style="margin: 0; color: var(--admin-dark); font-size: 0.9rem;">
                        <i class="fas fa-info-circle" style="color: var(--admin-primary);"></i>
                        Menampilkan <strong><?= $totalOrders ?></strong> pesanan 
                        <?php if ($search): ?> dengan kata kunci "<strong><?= htmlspecialchars($search) ?></strong>"<?php endif; ?>
                        <?php if ($status): ?> status <strong><?= $status ?></strong><?php endif; ?>
                        dari <strong><?= date('d/m/Y', strtotime($startDate)) ?></strong> sampai <strong><?= date('d/m/Y', strtotime($endDate)) ?></strong>
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Orders Table -->
            <div class="admin-table-container">
                <?php if (count($orders) > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>WhatsApp</th>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): 
                            $total = ($order['product_price'] ?? 0) * $order['quantity'];
                        ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($order['customer_name']) ?></strong>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $order['whatsapp']) ?>" 
                                   target="_blank"
                                   style="color: var(--admin-primary); text-decoration: none; font-weight: 500;"
                                   title="Chat via WhatsApp">
                                    <i class="fab fa-whatsapp"></i> <?= htmlspecialchars($order['whatsapp']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($order['product_name']): ?>
                                    <?= htmlspecialchars($order['product_name']) ?>
                                <?php else: ?>
                                    <span style="color: #999; font-style: italic;">Contact Form</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $order['quantity'] ?></td>
                            <td style="font-weight: 600; color: var(--admin-primary);">
                                <?php if ($total > 0): ?>
                                    Rp <?= number_format($total, 0, ',', '.') ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?= date('d/m/Y', strtotime($order['created_at'])) ?></small><br>
                                <small style="color: #999;"><?= date('H:i', strtotime($order['created_at'])) ?></small>
                            </td>
                            <td>
                                <span class="admin-badge badge-<?= $order['status'] ?>">
                                    <?= $order['status'] ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" onsubmit="return confirmUpdate(<?= $order['id'] ?>, '<?= $order['status'] ?>')">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <div style="display: flex; gap: 8px;">
                                        <select name="status" style="flex: 1; padding: 8px; border-radius: 6px; border: 2px solid var(--admin-border);">
                                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="processed" <?= $order['status'] === 'processed' ? 'selected' : '' ?>>Diproses</option>
                                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Selesai</option>
                                        </select>
                                        <button type="submit" name="update_status" class="admin-btn admin-btn-primary admin-btn-small">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="padding: 60px 20px; text-align: center;">
                    <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ddd; margin-bottom: 20px;"></i>
                    <h3 style="color: var(--admin-gray); margin-bottom: 10px;">
                        <?php if ($search || $status || $startDate != date('Y-m-d', strtotime('-30 days')) || $endDate != date('Y-m-d')): ?>
                            Tidak ada pesanan dengan filter yang dipilih
                        <?php else: ?>
                            Belum ada pesanan
                        <?php endif; ?>
                    </h3>
                    <?php if ($search || $status): ?>
                        <p style="color: var(--admin-gray);">Coba ubah filter atau kata kunci pencarian</p>
                        <a href="orders.php" class="admin-btn admin-btn-primary" style="margin-top: 15px;">
                            <i class="fas fa-redo"></i> Tampilkan Semua Pesanan
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="admin-pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $startPage + 4);
                
                if ($startPage > 1) {
                    echo '<a href="?page=1&start_date=' . $startDate . '&end_date=' . $endDate . '&status=' . $status . '&search=' . urlencode($search) . '">1</a>';
                    if ($startPage > 2) echo '<span>...</span>';
                }
                
                for ($i = $startPage; $i <= $endPage; $i++):
                    if ($i == $page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    <?php endif;
                endfor;
                
                if ($endPage < $totalPages) {
                    if ($endPage < $totalPages - 1) echo '<span>...</span>';
                    echo '<a href="?page=' . $totalPages . '&start_date=' . $startDate . '&end_date=' . $endDate . '&status=' . $status . '&search=' . urlencode($search) . '">' . $totalPages . '</a>';
                }
                ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page+1 ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>