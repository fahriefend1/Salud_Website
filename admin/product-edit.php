<?php
require_once '../includes/auth.php';
require_once '../includes/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: products.php');
    exit();
}

// Ambil data produk
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (int)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    
    // Validasi
    if (empty($name) || empty($description) || $price <= 0) {
        $error = "Nama, deskripsi, dan harga harus diisi dengan benar!";
    } else {
        // Upload gambar baru (jika ada)
        $imageName = $product['image'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/uploads/';
            
            // Hapus gambar lama jika bukan default
            if ($imageName !== 'default.jpg' && file_exists($uploadDir . $imageName)) {
                unlink($uploadDir . $imageName);
            }
            
            $fileName = basename($_FILES['image']['name']);
            $fileTmp = $_FILES['image']['tmp_name'];
            $fileSize = $_FILES['image']['size'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            // Validasi ekstensi file
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($fileExt, $allowedExt)) {
                $error = "Hanya file gambar (JPG, PNG, GIF, WebP) yang diperbolehkan";
            } elseif ($fileSize > 2097152) { // 2MB
                $error = "Ukuran file maksimal 2MB";
            } else {
                $imageName = time() . '_' . uniqid() . '.' . $fileExt;
                $uploadPath = $uploadDir . $imageName;
                
                if (!move_uploaded_file($fileTmp, $uploadPath)) {
                    $error = "Gagal mengupload gambar";
                    $imageName = $product['image']; // Kembali ke gambar lama
                }
            }
        }
        
        if (empty($error)) {
            // Update database
            try {
                $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ?");
                $stmt->execute([$name, $description, $price, $stock, $imageName, $id]);
                
                $success = "Produk berhasil diperbarui!";
                
                // Update data produk yang ditampilkan
                $product['name'] = $name;
                $product['description'] = $description;
                $product['price'] = $price;
                $product['stock'] = $stock;
                $product['image'] = $imageName;
            } catch (PDOException $e) {
                $error = "Gagal memperbarui produk: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin SALUD</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const info = document.getElementById('fileInfo');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                
                // Update file info
                info.innerHTML = `
                    <strong>File baru:</strong> ${file.name}<br>
                    <strong>Size:</strong> ${(file.size / 1024).toFixed(2)} KB<br>
                    <strong>Type:</strong> ${file.type}
                `;
                
                // Preview image
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                }
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
                info.innerHTML = 'Pilih file untuk mengganti gambar';
            }
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
                <p>Edit Produk</p>
            </div>
            
            <ul class="admin-nav">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Produk</a></li>
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
                <h1><i class="fas fa-edit"></i> Edit Produk</h1>
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <img src="../assets/images/admin-avatar.png" alt="Admin Avatar" 
                             onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['admin_name'] ?? 'Admin') ?>&background=FDC64E&color=1A1A1A'">
                    </div>
                    <div class="admin-info">
                        <h4><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></h4>
                        <p>Administrator</p>
                    </div>
                    <a href="products.php" class="admin-btn admin-btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Form Container -->
            <div class="admin-form-container">
                <?php if ($error): ?>
                    <div class="admin-alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="admin-alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= $success ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    
                    <div class="admin-form-group">
                        <label for="name">Nama Produk <span class="required">*</span></label>
                        <input type="text" id="name" name="name" 
                               value="<?= htmlspecialchars($product['name']) ?>" 
                               class="admin-form-control"
                               required>
                    </div>
                    
                    <div class="admin-form-group">
                        <label for="description">Deskripsi <span class="required">*</span></label>
                        <textarea id="description" name="description" 
                                  class="admin-form-control"
                                  required><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    
                    <div class="admin-form-group">
                        <label for="price">Harga (Rp) <span class="required">*</span></label>
                        <input type="number" id="price" name="price" 
                               value="<?= $product['price'] ?>" 
                               class="admin-form-control"
                               min="1000" step="500"
                               required>
                    </div>
                    
                    <div class="admin-form-group">
                        <label for="stock">Stok</label>
                        <input type="number" id="stock" name="stock" 
                               value="<?= $product['stock'] ?>" 
                               class="admin-form-control"
                               min="0" step="1">
                    </div>
                    
                    <div class="admin-form-group">
                        <label>Gambar Saat Ini</label>
                        <div style="text-align: center; margin-bottom: 20px;">
                            <img src="../assets/uploads/<?= htmlspecialchars($product['image']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 style="max-width: 200px; border-radius: 8px; border: 2px solid var(--admin-border);"
                                 onerror="this.src='../assets/images/default.jpg'">
                            <p style="color: #666; font-size: 0.9rem; margin-top: 8px;">
                                <?= $product['image'] == 'default.jpg' ? 'Gambar default' : 'Gambar saat ini' ?>
                            </p>
                        </div>
                        
                        <label for="image">Ganti Gambar (Opsional)</label>
                        <div class="admin-file-upload" onclick="document.getElementById('image').click()">
                            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                            <label for="image">
                                <i class="fas fa-cloud-upload-alt"></i> Klik untuk upload gambar baru
                            </label>
                            <div id="fileInfo" style="color: #666; font-size: 0.9rem; margin-top: 10px;">
                                Pilih file untuk mengganti gambar
                            </div>
                        </div>
                        <div class="admin-file-preview" id="imagePreview"></div>
                        <small style="color: #666; display: block; margin-top: 8px;">
                            Kosongkan jika tidak ingin mengganti gambar (Format: JPG, PNG, GIF, WebP, max 2MB)
                        </small>
                    </div>
                    
                    <div style="display: flex; gap: 15px; margin-top: 30px;">
                        <button type="submit" class="admin-btn admin-btn-primary" style="flex: 1;">
                            <i class="fas fa-save"></i> Update Produk
                        </button>
                        <a href="products.php" class="admin-btn admin-btn-secondary" style="flex: 1; text-align: center;">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>