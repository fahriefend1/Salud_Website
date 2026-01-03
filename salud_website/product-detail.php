<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/path-fix.php';

trackVisit();

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

// Proses form pemesanan
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    
    // Validasi
    if (empty($nama) || empty($whatsapp) || empty($quantity)) {
        $error = "Semua field harus diisi!";
    } elseif (!is_numeric($quantity) || $quantity < 1) {
        $error = "Jumlah harus angka dan minimal 1";
    } elseif ($product['stock'] > 0 && $quantity > $product['stock']) {
        $error = "Jumlah melebihi stok tersedia! Stok: " . $product['stock'] . " cup";
    } else {
        try {
            // Simpan ke database orders
            $stmt = $pdo->prepare("INSERT INTO orders (customer_name, whatsapp, product_id, quantity, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([$nama, $whatsapp, $id, $quantity]);
            
            $order_id = $pdo->lastInsertId();
            
            // Format pesan untuk Instagram
            $pesan = urlencode("Halo SALUD! 👋\n\nSaya ingin memesan:\n\n📦 Produk: " . $product['name'] . "\n💰 Harga: Rp " . number_format($product['price'], 0, ',', '.') . "/cup\n🔢 Jumlah: " . $quantity . " cup\n💰 Total: Rp " . number_format($product['price'] * $quantity, 0, ',', '.') . "\n\n📋 Data Pemesan:\n👤 Nama: " . $nama . "\n📱 WhatsApp: " . $whatsapp . "\n\n📝 ID Pesanan: #" . str_pad($order_id, 6, '0', STR_PAD_LEFT) . "\n\nMohon konfirmasi ketersediaan dan cara pembayarannya. Terima kasih! 😊");
            
            $success = "Data pesanan berhasil disimpan! Anda akan diarahkan ke Instagram...";
            
            // Simpan data ke session
            $_SESSION['order_data'] = [
                'order_id' => $order_id,
                'nama' => $nama,
                'whatsapp' => $whatsapp,
                'quantity' => $quantity,
                'product_name' => $product['name'],
                'product_price' => $product['price'],
                'total' => $product['price'] * $quantity
            ];
            
            // Redirect ke Instagram setelah 2 detik
            echo '<script>
                setTimeout(function() {
                    window.location.href = "https://instagram.com/saludby.tamanide";
                }, 2000);
            </script>';
            
        } catch (PDOException $e) {
            $error = "Gagal menyimpan pesanan. Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - SALUD</title>
    <!-- Preconnect untuk font dan CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Font dengan loading optimasi -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" 
          rel="stylesheet" 
          media="print" 
          onload="this.media='all'">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Font Awesome dengan defer -->
    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
          crossorigin="anonymous" 
          referrerpolicy="no-referrer" />
</head>
<body>
    <!-- Skip to content untuk accessibility -->
    <a href="#main-content" class="skip-to-content visually-hidden">Skip to main content</a>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container nav-container">
            <div class="logo-wrapper">
                <img src="assets/images/Logo Salad Puding Hitam.png" 
                     alt="SALUD Logo" 
                     class="logo-image"
                     width="40"
                     height="40">
                <a href="index.php" class="logo">SALUD</a>
            </div>

            <!-- Tambahkan button ini untuk mobile -->
            <button class="nav-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Product</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content">
        <!-- Product Detail -->
        <section class="features">
            <div class="container">
                <div class="product-detail-grid">
                    <!-- Product Image -->
                    <div class="product-image-container">
                        <div class="img-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                        <img src="assets/uploads/<?= htmlspecialchars($product['image']) ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             class="product-detail-img"
                             onerror="this.src='assets/images/default.jpg'"
                             loading="lazy">
                    </div>
                    
                    <!-- Product Info -->
                    <div class="product-info-container">
                        <h1><?= htmlspecialchars($product['name']) ?></h1>
                        
                        <div class="product-price-large">
                            Rp <?= number_format($product['price'], 0, ',', '.') ?> / cup
                        </div>
                        
                        <div class="stock-info">
                            <span class="product-stock <?= $product['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
                                <i class="fas fa-<?= $product['stock'] > 0 ? 'check' : 'times' ?>"></i>
                                <?= $product['stock'] > 0 ? 'Stok Tersedia: ' . $product['stock'] . ' cup' : 'Stok Habis' ?>
                            </span>
                        </div>
                        
                        <div class="product-description">
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </div>
                        
                        <!-- Order Form -->
                        <div class="form-container">
                            <?php if ($success): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> <?= $success ?>
                                    <p class="mt-1 mb-0"><small>Mengarahkan ke Instagram dalam 2 detik...</small></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-error">
                                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                                </div>
                            <?php endif; ?>
                            
                            <p class="mb-2">Isi data Anda, data akan disimpan dan Anda diarahkan ke Instagram.</p>
                            
                            <?php if ($product['stock'] > 0): ?>
                            <form method="POST" id="orderForm">
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap <span class="required">*</span></label>
                                    <input type="text" id="nama" name="nama" 
                                           value="<?= isset($_SESSION['order_data']['nama']) ? htmlspecialchars($_SESSION['order_data']['nama']) : (isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '') ?>" 
                                           required
                                           placeholder="Masukkan nama lengkap Anda">
                                </div>
                                
                                <div class="form-group">
                                    <label for="whatsapp">Nomor WhatsApp <span class="required">*</span></label>
                                    <input type="tel" id="whatsapp" name="whatsapp" 
                                           value="<?= isset($_SESSION['order_data']['whatsapp']) ? htmlspecialchars($_SESSION['order_data']['whatsapp']) : (isset($_POST['whatsapp']) ? htmlspecialchars($_POST['whatsapp']) : '') ?>" 
                                           placeholder="Contoh: 6281234567890" 
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="quantity">Jumlah (cup) <span class="required">*</span></label>
                                    <input type="number" id="quantity" name="quantity" 
                                           value="<?= isset($_SESSION['order_data']['quantity']) ? htmlspecialchars($_SESSION['order_data']['quantity']) : (isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : '1') ?>" 
                                           min="1" 
                                           max="<?= $product['stock'] ?>"
                                           required
                                           placeholder="Masukkan jumlah yang diinginkan">
                                    <small class="text-muted">Stok tersedia: <?= $product['stock'] ?> cup</small>
                                </div>
                                
                                <!-- Preview Harga -->
                                <div class="price-preview mb-2 p-2" style="background-color: var(--secondary); border-radius: 10px;">
                                    <div class="d-flex justify-between">
                                        <span><strong>Total Harga:</strong></span>
                                        <span id="subtotalPreview" style="font-weight: bold; color: var(--primary);">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                    </div>
                                    <small class="text-muted" id="detailPreview"><?= $product['name'] ?> × 1 cup</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fab fa-instagram"></i> Pesan Via Instagram
                                </button>
                                
                                <div class="text-center mt-2">
                                    <small class="text-muted">Data akan disimpan ke sistem dan Anda diarahkan ke Instagram</small>
                                </div>
                            </form>
                            <?php else: ?>
                                <div class="alert alert-error">
                                    <i class="fas fa-exclamation-triangle"></i> Maaf, stok produk ini sedang habis.
                                </div>
                                <a href="products.php" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i> Lihat Produk Lainnya
                                </a>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <div class="footer-logo">SALUD</div>
                    <p>Salad puding segar & lezat untuk mahasiswa dan pelajar. Dibuat dengan buah segar tanpa pengawet.</p>
                    <div class="social-icons">
                        <a href="https://www.instagram.com/saludby.tamanide?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="social-icon" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="social-icon" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h3>Menu</h3>
                    <ul>
                        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="products.php"><i class="fas fa-box"></i> Product</a></li>
                        <li><a href="about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
                        <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h3>Kontak</h3>
                    <ul>
                        <li><a href="mailto:saludbytamanide@gmail.com"><i class="fas fa-envelope"></i> saludbytamanide@gmail.com</a></li>
                        <li><a href="tel:+6281234567890"><i class="fas fa-phone"></i> +62 812-3456-7890</a></li>
                        <li><a><i class="fas fa-map-marker-alt"></i> Jl. Kapten Batu Sihombing 2, Deli Serdang</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>Privacy Policy | Terms & Conditions</p>
                <p>&copy; 2025 Taman Ide, All Rights Reserved</p>
            </div>
        </div>
    </footer>

<script>
// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    // Cek apakah di mobile
    function isMobile() {
        return window.innerWidth <= 768;
    }
    
    // Buat hamburger button jika belum ada dan di mobile
    const navContainer = document.querySelector('.nav-container');
    const navMenu = document.querySelector('.nav-menu');
    
    if (isMobile() && navContainer && navMenu) {
        // Cek apakah button sudah ada
        let navToggle = document.querySelector('.nav-toggle');
        
        if (!navToggle) {
            // Buat hamburger button
            navToggle = document.createElement('button');
            navToggle.className = 'nav-toggle';
            navToggle.id = 'navToggle';
            navToggle.innerHTML = '<i class="fas fa-bars"></i>';
            navToggle.setAttribute('aria-label', 'Toggle navigation menu');
            navToggle.setAttribute('aria-expanded', 'false');
            navContainer.appendChild(navToggle);
        }
        
        // Setup toggle functionality
        navToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            navMenu.classList.toggle('active');
            const isExpanded = navMenu.classList.contains('active');
            this.setAttribute('aria-expanded', isExpanded);
            this.innerHTML = isExpanded ? 
                '<i class="fas fa-times"></i>' : 
                '<i class="fas fa-bars"></i>';
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (navMenu.classList.contains('active')) {
                if (!navToggle.contains(event.target) && !navMenu.contains(event.target)) {
                    navMenu.classList.remove('active');
                    navToggle.setAttribute('aria-expanded', 'false');
                    navToggle.innerHTML = '<i class="fas fa-bars"></i>';
                }
            }
        });
        
        // Close menu when clicking a link
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
                navToggle.innerHTML = '<i class="fas fa-bars"></i>';
            });
        });
        
        // Hide button on desktop
        function handleResize() {
            if (window.innerWidth > 768) {
                navMenu.classList.remove('active');
                if (navToggle) {
                    navToggle.style.display = 'none';
                    navToggle.setAttribute('aria-expanded', 'false');
                    navToggle.innerHTML = '<i class="fas fa-bars"></i>';
                }
            } else {
                if (navToggle) {
                    navToggle.style.display = 'block';
                }
            }
        }
        
        // Initial check
        handleResize();
        
        // Listen for resize
        window.addEventListener('resize', handleResize);
    }
    
    // Handle image loading dengan placeholder
    function loadImages() {
        const images = document.querySelectorAll('img[loading="lazy"]');
        
        images.forEach(img => {
            const placeholder = img.previousElementSibling;
            
            if (img.complete) {
                // Image sudah loaded
                if (placeholder && placeholder.classList.contains('img-placeholder')) {
                    placeholder.style.display = 'none';
                }
            } else {
                // Image belum loaded
                img.addEventListener('load', function() {
                    if (placeholder && placeholder.classList.contains('img-placeholder')) {
                        placeholder.style.display = 'none';
                    }
                });
                
                img.addEventListener('error', function() {
                    console.log('Error loading image:', this.src);
                    if (placeholder && placeholder.classList.contains('img-placeholder')) {
                        placeholder.style.display = 'none';
                    }
                    
                    // Coba load fallback
                    if (this.getAttribute('onerror') && this.getAttribute('onerror').includes("src='")) {
                        const fallback = this.getAttribute('onerror').match(/src='([^']+)'/)[1];
                        if (fallback) {
                            this.src = fallback;
                        }
                    }
                });
            }
        });
    }
    
    // Jalankan image loading
    loadImages();
    
    // Load images tambahan setelah 100ms (fallback)
    setTimeout(loadImages, 100);
    
    // Preview harga saat jumlah diubah
    const quantityInput = document.getElementById('quantity');
    const subtotalPreview = document.getElementById('subtotalPreview');
    const detailPreview = document.getElementById('detailPreview');
    
    if (quantityInput && subtotalPreview) {
        // Dapatkan harga produk dari halaman
        const priceText = document.querySelector('.product-price-large').textContent;
        const priceMatch = priceText.match(/Rp\s([\d.,]+)/);
        const productName = document.querySelector('h1').textContent;
        
        if (priceMatch) {
            const price = parseFloat(priceMatch[1].replace(/\./g, '').replace(',', '.'));
            
            function updatePricePreview() {
                const quantity = parseInt(quantityInput.value) || 1;
                const subtotal = price * quantity;
                
                // Format ke Rupiah
                const formatter = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                });
                
                subtotalPreview.textContent = formatter.format(subtotal);
                detailPreview.textContent = productName + ' × ' + quantity + ' cup';
            }
            
            quantityInput.addEventListener('input', updatePricePreview);
            quantityInput.addEventListener('change', updatePricePreview);
            
            // Initial update
            updatePricePreview();
        }
    }
    
    // Validasi form sebelum submit
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            const nama = document.getElementById('nama').value.trim();
            const whatsapp = document.getElementById('whatsapp').value.trim();
            const quantity = document.getElementById('quantity').value.trim();
            
            if (!nama || !whatsapp || !quantity) {
                e.preventDefault();
                alert('Harap isi semua field yang diperlukan!');
                return;
            }
            
            if (isNaN(quantity) || quantity < 1) {
                e.preventDefault();
                alert('Jumlah harus berupa angka dan minimal 1!');
                return;
            }
            
            // Tampilkan konfirmasi
            if (!confirm('Data akan disimpan ke database dan Anda akan diarahkan ke Instagram. Lanjutkan?')) {
                e.preventDefault();
            }
        });
    }
});
</script>

</body>
</html>