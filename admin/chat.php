<?php
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Filter periode default (30 hari terakhir)
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// Validasi tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) $startDate = date('Y-m-d', strtotime('-30 days'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) $endDate = date('Y-m-d');

// 1. Setup Filter Dasar
$where = "WHERE DATE(created_at) BETWEEN :start AND :end";
$params = [
    ':start' => $startDate,
    ':end' => $endDate
];

// 2. Tambahkan Fitur Pencarian (Search)
$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $where .= " AND (name LIKE :s1 OR email LIKE :s2 OR whatsapp LIKE :s3 OR message LIKE :s4)";
    $search_term = "%$search%";
    $params[':s1'] = $search_term;
    $params[':s2'] = $search_term;
    $params[':s3'] = $search_term;
    $params[':s4'] = $search_term;
}

// 3. Tambahkan Filter Status (unread, replied, closed)
$status_filter = $_GET['status'] ?? '';
if (!empty($status_filter)) {
    $where .= " AND status = :status";
    $params[':status'] = $status_filter;
}

// 4. Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// 5. Hitung Total Data
$countQuery = "SELECT COUNT(*) as total FROM contacts $where";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalMessages = $countStmt->fetch()['total'];
$totalPages = ceil($totalMessages / $limit);

// 6. Ambil Data
$query = "SELECT * FROM contacts $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll();

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $msgId = (int)$_POST['order_id'];
    $newStatus = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE contacts SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $msgId]);
    
    header("Location: chat.php?success=Status pesan berhasil diperbarui&" . http_build_query($_GET));
    exit();
}

$success = $_GET['success'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Kontak - Admin SALUD</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <img src="../assets/images/Logo Salad Puding Hitam.png" alt="SALUD Logo" class="admin-logo-img" onerror="this.style.display='none'">
                <p>Kelola Pesan</p>
            </div>
            <ul class="admin-nav">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Produk</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li><a href="chat.php" class="active"><i class="fas fa-comments"></i> Pesan Kontak</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-eye"></i> Lihat Website</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1><i class="fas fa-comments"></i> Pesan Kontak</h1>
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

            <?php if ($success): ?>
                <div class="admin-alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="admin-filters">
                <form method="GET">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label><i class="far fa-calendar-alt"></i> Mulai</label>
                            <input type="date" name="start_date" value="<?= $startDate ?>" class="admin-form-control">
                        </div>
                        <div class="filter-group">
                            <label><i class="far fa-calendar-alt"></i> Akhir</label>
                            <input type="date" name="end_date" value="<?= $endDate ?>" class="admin-form-control">
                        </div>
                        <div class="filter-group">
                            <label><i class="fas fa-filter"></i> Status</label>
                            <select name="status" class="admin-form-control">
                                <option value="">Semua Status</option>
                                <option value="unread" <?= $status_filter === 'unread' ? 'selected' : '' ?>>Unread</option>
                                <option value="replied" <?= $status_filter === 'replied' ? 'selected' : '' ?>>Replied</option>
                                <option value="closed" <?= $status_filter === 'closed' ? 'selected' : '' ?>>Closed</option>
                            </select>
                        </div>
                        <div class="filter-group" style="flex: 0 0 auto; display: flex; gap: 10px; align-items: flex-end;">
                            <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-filter"></i> Filter</button>
                            <a href="chat.php" class="admin-btn admin-btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; align-items: center; margin-top: 15px;">
                        <div style="flex: 1; position: relative;">
                            <input type="text" name="search" 
                                placeholder="Cari nama, email, WhatsApp, atau isi pesan..." 
                                value="<?= htmlspecialchars($search) ?>"
                                class="admin-form-control"
                                style="padding-left: 40px; width: 100%;">
                            <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                        </div>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>

            <div class="admin-table-container">
                <?php if (count($messages) > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama & Email</th>
                            <th>WhatsApp</th>
                            <th>Pesan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td>#<?= $msg['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($msg['name'] ?? 'No Name') ?></strong><br>
                                <small style="color: #666;"><?= htmlspecialchars($msg['email'] ?? '-') ?></small>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $msg['whatsapp']) ?>" target="_blank" style="color: #25D366; text-decoration: none;">
                                    <i class="fab fa-whatsapp"></i> <?= htmlspecialchars($msg['whatsapp']) ?>
                                </a>
                            </td>
                            <td>
                                <div style="max-width: 250px; font-size: 0.85rem; color: #444;">
                                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                </div>
                            </td>
                            <td>
                                <span class="admin-badge badge-<?= $msg['status'] ?>">
                                    <?= ucfirst($msg['status']) ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="order_id" value="<?= $msg['id'] ?>">
                                    <div style="display: flex; gap: 5px;">
                                        <select name="status" class="admin-form-control" style="padding: 5px; font-size: 0.8rem;">
                                            <option value="unread" <?= $msg['status'] === 'unread' ? 'selected' : '' ?>>Unread</option>
                                            <option value="replied" <?= $msg['status'] === 'replied' ? 'selected' : '' ?>>Replied</option>
                                            <option value="closed" <?= $msg['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                        </select>
                                        <button type="submit" name="update_status" class="admin-btn admin-btn-primary admin-btn-small">
                                            <i class="fas fa-check"></i>
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
                        <i class="fas fa-comments" style="font-size: 3rem; color: #ddd; margin-bottom: 20px;"></i>
                        <h3 style="color: #999;">Tidak ada pesan ditemukan</h3>
                    </div>
                <?php endif; ?>
            </div>
            </main>
    </div>
</body>
</html>