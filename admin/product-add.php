<?php
require_once '../includes/auth.php';
require_once '../includes/database.php';

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
        // Upload gambar
        $imageName = 'default.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/uploads/';
            
            // Buat folder jika belum ada
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
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
                
                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    // Success
                } else {
                    $error = "Gagal mengupload gambar";
                    $imageName = 'default.jpg';
                }
            }
        }
        
        if (empty($error)) {
            // Simpan ke database
            try {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $stock, $imageName]);
                
                $success = "Produk berhasil ditambahkan!";
                $_POST = []; // Reset form
            } catch (PDOException $e) {
                $error = "Gagal menyimpan produk: " . $e->getMessage();
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
    <title>Tambah Produk - Admin SALUD</title>
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
                    <strong>File:</strong> ${file.name}<br>
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
                info.innerHTML = 'Belum ada file dipilih';
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
                <p>Tambah Produk</p>
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
                <h1><i class="fas fa-plus-circle"></i> Tambah Produk Baru</h1>
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
                    <div class="admin-form-group">
                        <label for="name">Nama Produk <span class="required">*</span></label>
                        <input type="text" id="name" name="name" 
                               value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" 
                               class="admin-form-control"
                               placeholder="Contoh: Puding Rasa Mangga"
                               required>
                    </div>
                    
                    <div class="admin-form-group">
                        <label for="description">Deskripsi <span class="required">*</span></label>
                        <textarea id="description" name="description" 
                                  class="admin-form-control"
                                  placeholder="Deskripsi lengkap produk..."
                                  required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                    </div>
                    
                    <div class="admin-form-group">
                        <label for="price">Harga (Rp) <span class="required">*</span></label>
                        <input type="number" id="price" name="price" 
                               value="<?= isset($_POST['price']) ? $_POST['price'] : '' ?>" 
                               class="admin-form-control"
                               placeholder="10000"
                               min="1000" step="500"
                               required>
                    </div>
                    
                    <div class="admin-form-group">
                        <label for="stock">Stok Awal</label>
                        <input type="number" id="stock" name="stock" 
                               value="<?= isset($_POST['stock']) ? $_POST['stock'] : '0' ?>" 
                               class="admin-form-control"
                               placeholder="0"
                               min="0" step="1">
                    </div>
                    
                    <div class="admin-form-group">
                        <label for="image">Gambar Produk</label>
                        <div class="admin-file-upload" onclick="document.getElementById('image').click()">
                            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                            <label for="image">
                                <i class="fas fa-cloud-upload-alt"></i> Klik untuk upload gambar
                            </label>
                            <div id="fileInfo" style="color: #666; font-size: 0.9rem; margin-top: 10px;">
                                Belum ada file dipilih
                            </div>
                        </div>
                        <div class="admin-file-preview" id="imagePreview"></div>
                        <small style="color: #666; display: block; margin-top: 8px;">
                            Format: JPG, PNG, GIF, WebP (max 2MB). Kosongkan untuk menggunakan gambar default.
                        </small>
                    </div>
                    
                    <div style="display: flex; gap: 15px; margin-top: 30px;">
                        <button type="submit" class="admin-btn admin-btn-primary" style="flex: 1;">
                            <i class="fas fa-save"></i> Simpan Produk
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