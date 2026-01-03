<?php
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Search functionality
$search = $_GET['search'] ?? '';
$where = '';
$params = [];

if (!empty($search)) {
    $where = "WHERE name LIKE ? OR description LIKE ?";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total
$countQuery = "SELECT COUNT(*) as total FROM products";
if ($where) {
    $countQuery .= " $where";
}
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalProducts = $countStmt->fetch()['total'];
$totalPages = ceil($totalProducts / $limit);

// Ambil data
$query = "SELECT * FROM products";
if ($where) {
    $query .= " $where";
}
$query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Delete product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Hapus gambar jika ada
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if ($product && $product['image'] != 'default.jpg' && file_exists("../assets/uploads/" . $product['image'])) {
        unlink("../assets/uploads/" . $product['image']);
    }
    
    // Hapus dari database
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: products.php?success=Produk berhasil dihapus");
    exit();
}

$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin SALUD</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <img src="../assets/images/Logo Salad Puding Hitam.png" alt="SALUD Logo" class="admin-logo-img"
                     onerror="this.style.display='none'">
                <p>Kelola Produk</p>
            </div>
            
            <ul class="admin-nav">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="products.php" class="active"><i class="fas fa-box"></i> Produk</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li><a href="chat.php"><i class="fas fa-comments"></i> Pesan Kontak</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-eye"></i> Lihat Website</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <div class="admin-header">
                <h1><i class="fas fa-boxes"></i> Kelola Produk</h1>
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

            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="admin-alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- Action Bar -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 15px;">
                <a href="product-add.php" class="admin-btn admin-btn-primary" style="white-space: nowrap;">
                    <i class="fas fa-plus"></i> Tambah Produk
                </a>
                
                <!-- Search -->
                <form method="GET" style="display: flex; gap: 10px; align-items: center; flex: 1; max-width: 500px;">
                    <div style="flex: 1; position: relative;">
                        <input type="text" name="search" 
                            placeholder="Cari produk..." 
                            value="<?= htmlspecialchars($search) ?>"
                            class="admin-form-control"
                            style="padding-left: 40px; width: 100%;">
                        <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                    </div>
                    <button type="submit" class="admin-btn admin-btn-secondary" style="white-space: nowrap;">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <?php if ($search): ?>
                        <a href="products.php" class="admin-btn admin-btn-danger" style="white-space: nowrap;">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Products Table -->
            <div class="admin-table-container">
                <?php if (count($products) > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="70">Gambar</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Tanggal</th>
                            <th width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <img src="../assets/uploads/<?= htmlspecialchars($product['image']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     onerror="this.src='../assets/images/default.jpg'">
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($product['name']) ?></strong><br>
                                <small style="color: #666; font-size: 0.85rem;">
                                    <?= htmlspecialchars(substr($product['description'], 0, 60)) ?>...
                                </small>
                            </td>
                            <td style="font-weight: 600; color: var(--admin-primary);">
                                Rp <?= number_format($product['price'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <span style="display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: <?= $product['stock'] > 0 ? 'rgba(52, 211, 153, 0.1)' : 'rgba(248, 113, 113, 0.1)' ?>; color: <?= $product['stock'] > 0 ? '#065F46' : '#991B1B' ?>; border: 1px solid <?= $product['stock'] > 0 ? 'rgba(52, 211, 153, 0.3)' : 'rgba(248, 113, 113, 0.3)' ?>;">
                                    <?= $product['stock'] ?> cup
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($product['created_at'])) ?></td>
                            <td>
                                <div class="admin-btn-group">
                                    <a href="product-edit.php?id=<?= $product['id'] ?>" 
                                       class="admin-btn admin-btn-secondary admin-btn-small">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?= $product['id'] ?>" 
                                       class="admin-btn admin-btn-danger admin-btn-small"
                                       onclick="return confirm('Hapus produk <?= htmlspecialchars(addslashes($product['name'])) ?>?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="padding: 60px 20px; text-align: center;">
                    <i class="fas fa-box-open" style="font-size: 3rem; color: #ddd; margin-bottom: 20px;"></i>
                    <h3 style="color: var(--admin-gray); margin-bottom: 10px;">
                        <?= $search ? 'Produk tidak ditemukan' : 'Belum ada produk' ?>
                    </h3>
                    <?php if ($search): ?>
                        <p style="color: var(--admin-gray);">Coba dengan kata kunci lain</p>
                        <a href="products.php" class="admin-btn admin-btn-primary" style="margin-top: 15px;">
                            <i class="fas fa-redo"></i> Tampilkan Semua
                        </a>
                    <?php else: ?>
                        <p style="color: var(--admin-gray);">Mulai dengan menambahkan produk pertama</p>
                        <a href="product-add.php" class="admin-btn admin-btn-primary" style="margin-top: 15px;">
                            <i class="fas fa-plus"></i> Tambah Produk Pertama
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="admin-pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $startPage + 4);
                
                if ($startPage > 1) {
                    echo '<a href="?page=1&search=' . urlencode($search) . '">1</a>';
                    if ($startPage > 2) echo '<span>...</span>';
                }
                
                for ($i = $startPage; $i <= $endPage; $i++):
                    if ($i == $page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    <?php endif;
                endfor;
                
                if ($endPage < $totalPages) {
                    if ($endPage < $totalPages - 1) echo '<span>...</span>';
                    echo '<a href="?page=' . $totalPages . '&search=' . urlencode($search) . '">' . $totalPages . '</a>';
                }
                ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>