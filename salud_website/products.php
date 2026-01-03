<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/path-fix.php';

trackVisit();

// Pagination
$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total produk
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE stock > 0");
$totalProducts = $stmt->fetch()['total'];
$totalPages = ceil($totalProducts / $limit);

// Ambil produk
$stmt = $pdo->prepare("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - SALUD</title>
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
                <li><a href="products.php" class="active">Product</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content">
        <!-- Page Header (Teks di tengah) -->
        <section class="hero text-center">
            <div class="container">
                <h1>Pilih Salad Puding<br>Favoritmu</h1>
                <p>Temukan varian rasa segar yang cocok untuk seleramu</p>
            </div>
        </section>

        <!-- Products Grid -->
        <section class="products-section">
            <div class="container">
                <?php if (count($products) > 0): ?>
                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
                        <article class="product-card">
                            <!-- Tambahkan wrapper untuk gambar -->
                            <div class="product-img-wrapper">
                                <div class="img-placeholder">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <img src="assets/uploads/<?= htmlspecialchars($product['image']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     class="product-img"
                                     onerror="this.src='assets/images/default.jpg'" 
                                     loading="lazy">
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <p><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                                <div class="product-price">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>
                                <span class="product-stock in-stock">
                                    <i class="fas fa-check"></i> Stok: <?= $product['stock'] ?> cup
                                </span>
                                <a href="product-detail.php?id=<?= $product['id'] ?>" class="btn btn-secondary btn-block">
                                    Lihat Detail
                                </a>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" aria-label="Previous page"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="current"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>" aria-label="Next page"><i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="no-products text-center py-3">
                        <i class="fas fa-box-open"></i>
                        <h3>Belum ada produk tersedia</h3>
                        <p>Silahkan kembali lagi nanti</p>
                        <a href="index.php" class="btn btn-primary mt-2">Kembali ke Home</a>
                    </div>
                <?php endif; ?>
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
});
</script>

</body>
</html>