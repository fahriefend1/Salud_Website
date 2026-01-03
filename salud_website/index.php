<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/path-fix.php';

// Tracking kunjungan (sudah dioptimasi)
trackVisit();

// Optimasi query: ambil produk dengan cache sederhana
$featuredProducts = [];
$cacheKey = 'featured_products_' . date('Y-m-d-H');

// Cek session cache dulu
if (isset($_SESSION[$cacheKey])) {
    $featuredProducts = $_SESSION[$cacheKey];
} else {
    try {
        // Query produk dengan kolom yang dibutuhkan saja
        $stmt = $pdo->prepare("
            SELECT id, name, description, price, stock, image 
            FROM products 
            WHERE stock > 0 
            ORDER BY created_at DESC 
            LIMIT 6
        ");
        $stmt->execute();
        $featuredProducts = $stmt->fetchAll();
        
        // Cache di session untuk 1 jam
        $_SESSION[$cacheKey] = $featuredProducts;
    } catch (PDOException $e) {
        // Jika error, tetap tampilkan halaman dengan array kosong
        error_log("Product query error: " . $e->getMessage());
        $featuredProducts = [];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALUD - Salad Puding Segar & Lezat</title>
    
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

    <!-- Navigation with Logo -->
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
        
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="products.php">Product</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content">
        <!-- Hero Section with Image -->
        <section class="hero">
            <div class="container hero-with-image">
                <div class="hero-content">
                    <h1>Nikmati Salad<br>Puding Segar & Lezat<br>Setiap Hari</h1>
                    <p>Dibuat dari buah segar, puding lembut, dan topping menarik. Cocok untuk menemani aktivitas mahasiswa dan pelajar.</p>
                    <a href="products.php" class="btn btn-primary">Lihat Produk Kami</a>
                </div>
                <div class="hero-image">
                    <div class="img-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                    <img src="assets/images/Puding1.png" alt="Salad Puding SALUD" 
                         onerror="this.src='assets/images/default.jpg'" 
                         loading="lazy">
                </div>
            </div>
        </section>

        <!-- Kelebihan Produk (5 items) -->
        <section class="features">
            <div class="container">
                <h2 class="section-title">Kelebihan Produk</h2>
                <div class="features-grid-5">
                    <div class="feature-card-5">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h3>Sehat & Segar</h3>
                        <p>Dibuat dari buah segar pilihan tanpa bahan pengawet atau pemanis buatan.</p>
                    </div>
                    <div class="feature-card-5">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Siap Saji</h3>
                        <p>Praktis dan mudah dibawa, cocok untuk mahasiswa yang sibuk dengan aktivitas.</p>
                    </div>
                    <div class="feature-card-5">
                        <div class="feature-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h3>Harga Terjangkau</h3>
                        <p>Harga ramah di kantong pelajar dengan kualitas premium.</p>
                    </div>
                    <div class="feature-card-5">
                        <div class="feature-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Rasa Premium</h3>
                        <p>Perpaduan sempurna antara manis, asam, dan segar buah-buahan.</p>
                    </div>
                    <div class="feature-card-5">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h3>Pengiriman Cepat</h3>
                        <p>Dikirim dalam keadaan dingin untuk menjaga kesegaran produk.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Produk Unggulan -->
        <section class="products-section">
            <div class="container">
                <h2 class="section-title">Puding Unggulan Kami</h2>
                <div class="products-grid">
                    <?php if (count($featuredProducts) > 0): ?>
                        <?php foreach ($featuredProducts as $product): ?>
                        <article class="product-card">
                            <div class="img-placeholder">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <img src="assets/uploads/<?= htmlspecialchars($product['image']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 class="product-img"
                                 onerror="this.src='assets/images/default.jpg'" 
                                 loading="lazy">
                            <div class="product-info">
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <p><?= htmlspecialchars(substr($product['description'], 0, 80)) ?>...</p>
                                <div class="product-price">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>
                                <span class="product-stock <?= $product['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
                                    <i class="fas fa-<?= $product['stock'] > 0 ? 'check' : 'times' ?>"></i>
                                    <?= $product['stock'] > 0 ? 'Stok Tersedia' : 'Stok Habis' ?>
                                </span>
                                <a href="product-detail.php?id=<?= $product['id'] ?>" class="btn btn-secondary">Lihat Detail</a>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-products text-center">
                            <i class="fas fa-box-open"></i>
                            <h3>Belum ada produk tersedia</h3>
                            <a href="contact.php" class="btn btn-primary">Hubungi Kami</a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (count($featuredProducts) > 0): ?>
                <div class="text-center mt-2">
                    <a href="products.php" class="btn btn-primary">
                        <i class="fas fa-list"></i> Lihat Semua Produk
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Enhanced Slideshow -->
        <section class="slideshow">
            <div class="container">
                <h2 class="section-title">Galeri Produk</h2>
                <div class="slideshow-container">
                    <div class="slideshow-controls">
                        <button class="slide-btn prev-btn" aria-label="Previous slide">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="slide-btn next-btn" aria-label="Next slide">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    
                    <div class="slide active">
                        <div class="img-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                        <img src="assets/images/Hero Slider Puding.png" alt="Macam Macam Puding" 
                             onerror="this.src='assets/images/default.jpg'" 
                             loading="lazy">
                        <div class="slide-caption">
                            <h3>Macam-macam Puding SALUD</h3>
                            <p>Segar dengan potongan buah mangga asli</p>
                        </div>
                    </div>
                    <div class="slide">
                        <div class="img-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                        <img src="assets/images/Kelebihan Produk Salud.png" alt="Kelebihan Produk Salud" 
                             onerror="this.src='assets/images/default.jpg'" 
                             loading="lazy">
                        <div class="slide-caption">
                            <h3>Kelebihan Produk SALUD</h3>
                            <p>Manis dan asam yang seimbang</p>
                        </div>
                    </div>
                    <!-- <div class="slide">
                        <div class="img-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                        <img src="assets/images/puding-anggur.jpg" alt="Puding Anggur" 
                             onerror="this.src='assets/images/default.jpg'" 
                             loading="lazy">
                        <div class="slide-caption">
                            <h3>Puding Rasa Anggur</h3>
                            <p>Rasa anggur alami yang menyegarkan</p>
                        </div>
                    </div>
                    <div class="slide">
                        <div class="img-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                        <img src="assets/images/salad-puding.jpg" alt="Salad Puding" 
                             onerror="this.src='assets/images/default.jpg'" 
                             loading="lazy">
                        <div class="slide-caption">
                            <h3>Salad Puding Mix Buah</h3>
                            <p>Campuran berbagai buah segar</p>
                        </div>
                    </div>
                    <div class="slide">
                        <div class="img-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                        <img src="assets/images/puding-mangga.jpg" alt="Puding Mangga" 
                             onerror="this.src='assets/images/default.jpg'" 
                             loading="lazy">
                        <div class="slide-caption">
                            <h3>Puding Rasa Mangga</h3>
                            <p>Rasa mangga manis dan segar</p>
                        </div>
                    </div> -->
                </div>
                <div class="slide-nav">
                    <span class="dot active" data-slide="1" role="button" tabindex="0"></span>
                    <span class="dot" data-slide="2" role="button" tabindex="0"></span>
                    <!-- <span class="dot" data-slide="3" role="button" tabindex="0"></span>
                    <span class="dot" data-slide="4" role="button" tabindex="0"></span>
                    <span class="dot" data-slide="5" role="button" tabindex="0"></span> -->
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

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Mobile Menu Toggle (penting untuk CSS baru)
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        // Create hamburger button jika belum ada
        if (!navToggle) {
            const newToggle = document.createElement('button');
            newToggle.className = 'nav-toggle';
            newToggle.innerHTML = '<i class="fas fa-bars"></i>';
            newToggle.setAttribute('aria-label', 'Toggle navigation menu');
            document.querySelector('.nav-container').appendChild(newToggle);
        }
        
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
            if (navMenu && navMenu.classList.contains('active')) {
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
                if (navToggle) {
                    navToggle.setAttribute('aria-expanded', 'false');
                    navToggle.innerHTML = '<i class="fas fa-bars"></i>';
                }
            });
        });
    }
    
    // 2. Image Loading (lebih sederhana)
    function handleImageLoading() {
        document.querySelectorAll('img[loading="lazy"]').forEach(img => {
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
    handleImageLoading();
    
    // 3. Slideshow Functionality (optimized)
    let slideIndex = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    let slideInterval;
    
    function showSlide(index) {
        if (!slides.length) return;
        
        // Wrap around
        if (index >= slides.length) slideIndex = 0;
        if (index < 0) slideIndex = slides.length - 1;
        
        // Hide all slides
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        // Show current slide
        slides[slideIndex]?.classList.add('active');
        dots[slideIndex]?.classList.add('active');
    }
    
    function nextSlide() {
        slideIndex++;
        showSlide(slideIndex);
    }
    
    function prevSlide() {
        slideIndex--;
        showSlide(slideIndex);
    }
    
    // Setup slideshow if exists
    if (slides.length > 0) {
        // Show first slide
        showSlide(slideIndex);
        
        // Navigation buttons
        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                prevSlide();
                resetAutoSlide();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                nextSlide();
                resetAutoSlide();
            });
        }
        
        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', (e) => {
                e.preventDefault();
                slideIndex = index;
                showSlide(slideIndex);
                resetAutoSlide();
            });
            
            // Keyboard support for dots
            dot.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    slideIndex = index;
                    showSlide(slideIndex);
                    resetAutoSlide();
                }
            });
        });
        
        // Auto slide
        function startAutoSlide() {
            slideInterval = setInterval(nextSlide, 5000);
        }
        
        function resetAutoSlide() {
            clearInterval(slideInterval);
            startAutoSlide();
        }
        
        startAutoSlide();
        
        // Pause on hover
        const slideshowContainer = document.querySelector('.slideshow-container');
        if (slideshowContainer) {
            slideshowContainer.addEventListener('mouseenter', () => {
                clearInterval(slideInterval);
            });
            
            slideshowContainer.addEventListener('mouseleave', () => {
                startAutoSlide();
            });
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                prevSlide();
                resetAutoSlide();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                nextSlide();
                resetAutoSlide();
            }
        });
    }
    
    // 4. Add loading animation removal after all images loaded
    window.addEventListener('load', function() {
        // Remove any remaining placeholders after 2 seconds (fallback)
        setTimeout(() => {
            document.querySelectorAll('.img-placeholder').forEach(placeholder => {
                const img = placeholder.nextElementSibling;
                if (img && img.tagName === 'IMG') {
                    placeholder.style.display = 'none';
                }
            });
        }, 2000);
    });
    
    // 5. Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href === '#' || href.startsWith('#main-content')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});
</script>
</body>
</html>