<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/path-fix.php';

trackVisit();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - SALUD</title>
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
                <li><a href="about.php" class="active">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content">
        <!-- Page Header (Teks di tengah) -->
        <section class="hero text-center">
            <div class="container">
                <h1>Cerita di Balik<br>Salad Puding Kami</h1>
                <p>Mengenal lebih dekat tentang SALUD dan tim Taman Ide</p>
            </div>
        </section>

        <!-- About Content -->
        <section class="features">
            <div class="container">
                <div class="form-container">
                    <div class="text-center mb-3">
                        <h2>Tentang SALUD</h2>
                    </div>
                    
                    <div class="about-description">
                        <p>
                            SALUD adalah produk dessert yang dikembangkan oleh tim mahasiswa Taman Ide sebagai bagian dari mata kuliah Entrepreneur. Kami berasal dari berbagai latar belakang, namun memiliki tujuan yang sama yaitu menciptakan camilan yang sehat, segar, dan bermanfaat bagi konsumen, terutama mahasiswa dan pelajar.
                        </p>
                        <p>
                            Kami percaya bahwa camilan sehat tidak harus membosankan. Karena itu, kami mengembangkan SALUD Puding Salad, sebuah inovasi yang menggunakan kelembutan puding dengan kesegaran buah asli, tanpa menggunakan pemanis buatan atau bahan pengawet.
                        </p>
                    </div>
                    
                    <!-- Vision & Mission Side by Side -->
                    <div class="vision-mission-side">
                        <div class="vision-container">
                            <div class="vision-card">
                                <h3><i class="fas fa-bullseye"></i> Visi</h3>
                                <ul>
                                    <li>Menciptakan camilan sehat, segar, dan terjangkau yang disukai remaja</li>
                                    <li>Membawa pengalaman makan yang lebih baik</li>
                                    <li>Menjadi brand salad puding terpercaya di kalangan pelajar</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mission-container">
                            <div class="mission-card">
                                <h3><i class="fas fa-flag-checkered"></i> Misi</h3>
                                <ul>
                                    <li>Menghasilkan produk puding salad yang aman tanpa bahan pengawet</li>
                                    <li>Melakukan riset pasar untuk meningkatkan kualitas produk</li>
                                    <li>Memberikan pelayanan terbaik kepada konsumen</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mb-3 mt-4">
                        <h2>Profil Anggota Tim</h2>
                        <p>Tim SALUD terdiri dari 6 anggota dengan berbagai keahlian</p>
                    </div>
                    
                    <!-- Team Grid 3 kolom 2 baris -->
                    <div class="team-grid-3x2">
                        <div class="team-card-no-hover">
                            <div class="team-photo">
                                <img src="assets/images/team/fahri.jpg" 
                                    alt="Fahri Efendi" 
                                    class="team-img"
                                    onerror="this.src='assets/images/default-team.jpg'"
                                    loading="lazy">
                            </div>
                            <h3>Fahri Efendi</h3>
                            <div class="team-role">Leader</div>
                            <p>Bertanggung jawab atas keseluruhan proyek dan pengembangan website</p>
                        </div>
                        
                        <div class="team-card-no-hover">
                            <div class="team-photo">
                                <img src="assets/images/team/rahmatika.jpg" 
                                    alt="Rahmatika Awalya" 
                                    class="team-img"
                                    onerror="this.src='assets/images/default-team.jpg'"
                                    loading="lazy">
                            </div>
                            <h3>Rahmatika Awalya</h3>
                            <div class="team-role">Bendahara</div>
                            <p>Mengelola keuangan dan pencatatan transaksi</p>
                        </div>
                        
                        <div class="team-card-no-hover">
                            <div class="team-photo">
                                <img src="assets/images/team/mauliah.jpg" 
                                    alt="Mauliah Zahara" 
                                    class="team-img"
                                    onerror="this.src='assets/images/default-team.jpg'"
                                    loading="lazy">
                            </div>
                            <h3>Mauliah Zahara</h3>
                            <div class="team-role">Tim Produksi</div>
                            <p>Bertanggung jawab atas produksi dan kualitas produk</p>
                        </div>
                        
                        <div class="team-card-no-hover">
                            <div class="team-photo">
                                <img src="assets/images/team/ilham.jpg" 
                                    alt="M Ilham Bintang S" 
                                    class="team-img"
                                    onerror="this.src='assets/images/default-team.jpg'"
                                    loading="lazy">
                            </div>
                            <h3>M Ilham Bintang S</h3>
                            <div class="team-role">Tim Pemasaran</div>
                            <p>Mengelola pemasaran dan promosi produk</p>
                        </div>
                        
                        <div class="team-card-no-hover">
                            <div class="team-photo">
                                <img src="assets/images/team/marcela.jpg" 
                                    alt="Marcela Buulolo" 
                                    class="team-img"
                                    onerror="this.src='assets/images/default-team.jpg'"
                                    loading="lazy">
                            </div>
                            <h3>Marcela Buulolo</h3>
                            <div class="team-role">Tim Pemasaran</div>
                            <p>Mengelola media sosial dan konten pemasaran</p>
                        </div>
                        
                        <div class="team-card-no-hover">
                            <div class="team-photo">
                                <img src="assets/images/team/sari.jpg" 
                                    alt="Sari Magdalena LumbanRaja" 
                                    class="team-img"
                                    onerror="this.src='assets/images/default-team.jpg'"
                                    loading="lazy">
                            </div>
                            <h3>Sari Magdalena LumbanRaja</h3>
                            <div class="team-role">Tim Pemasaran</div>
                            <p>Mengembangkan strategi pemasaran dan customer service</p>
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
});
</script>

</body>
</html>