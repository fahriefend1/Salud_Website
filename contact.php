<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/path-fix.php';

trackVisit();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validasi
    if (empty($first_name) || empty($last_name) || empty($email) || empty($whatsapp) || empty($message)) {
        $error = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        try {
            $full_name = $first_name . ' ' . $last_name;
            
            // Perbaikan: Gunakan nama tabel 'contacts' dan kolom yang sesuai
            $stmt = $pdo->prepare("
                INSERT INTO contacts 
                (name, email, whatsapp, message, status) 
                VALUES (?, ?, ?, ?, 'unread')
            ");
            
            $stmt->execute([
                $full_name, 
                $email, 
                $whatsapp, 
                $message
            ]);
            
            $contact_id = $pdo->lastInsertId();
            $success = "Pesan berhasil dikirim! ID Pesan: #" . $contact_id;
            $_POST = []; 
            
        } catch (PDOException $e) {
            $error = "Gagal mengirim pesan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - SALUD</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

    <nav class="navbar">
        <div class="container nav-container">
            <div class="logo-wrapper">
                <img src="assets/images/Logo Salad Puding Hitam.png" alt="SALUD Logo" class="logo-image" width="40" height="40">
                <a href="index.php" class="logo">SALUD</a>
            </div>

            <button class="nav-toggle" id="navToggle" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Product</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php" class="active">Contact</a></li>
            </ul>
        </div>
    </nav>

    <main id="main-content">
        <section class="hero text-center">
            <div class="container">
                <h1>Hubungi Kami</h1>
                <p>Ada pertanyaan atau ingin melakukan pemesanan khusus? Kami siap membantu Anda menikmati kesegaran SALUD.</p>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <div class="contact-grid">
                    
                    <div class="form-container">
                        <h2 class="mb-2">Kirim Pesan</h2>
                        <p class="mb-4" style="color: var(--gray);">Isi formulir di bawah ini untuk menghubungi kami</p>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <?= $success ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-error">
                                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">Nama Depan <span style="color: red;">*</span></label>
                                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required placeholder="Nama depan" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Nama Belakang <span style="color: red;">*</span></label>
                                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required placeholder="Nama belakang" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email <span style="color: red;">*</span></label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required placeholder="contoh@email.com" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                            </div>
                            
                            <div class="form-group">
                                <label for="whatsapp">Nomor WhatsApp <span style="color: red;">*</span></label>
                                <input type="tel" id="whatsapp" name="whatsapp" value="<?= htmlspecialchars($_POST['whatsapp'] ?? '') ?>" placeholder="08**********" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Pesan atau Pertanyaan <span style="color: red;">*</span></label>
                                <textarea id="message" name="message" required placeholder="Tuliskan pesan Anda di sini..." rows="5" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Kirim Pesan Sekarang
                            </button>
                        </form>
                    </div>
                    
                    <!-- Contact Info & Maps -->
                    <div class="contact-info-container">
                        <h2 class="mb-2">Informasi Kontak</h2>
                        <p class="mb-3">Hubungi kami melalui berbagai cara di bawah ini</p>
                        
                        <div class="contact-info-grid">
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-content">
                                    <h3>Email</h3>
                                    <p>saludbytamanide@gmail.com</p>
                                    <p class="contact-note">Biasanya merespon dalam 1-2 jam kerja</p>
                                    <a href="mailto:saludbytamanide@gmail.com" class="contact-link">
                                        <i class="fas fa-envelope"></i> Kirim Email
                                    </a>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div class="contact-content">
                                    <h3>WhatsApp</h3>
                                    <p>+62 812-3456-7890</p>
                                    <p class="contact-note">Jam operasional: 12.00 – 15.00 WIB</p>
                                    <a href="https://wa.me/6281234567890" class="contact-link" target="_blank">
                                        <i class="fab fa-whatsapp"></i> Chat via WhatsApp
                                    </a>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fab fa-instagram"></i>
                                </div>
                                <div class="contact-content">
                                    <h3>Instagram</h3>
                                    <p>@saludby.tamanide</p>
                                    <p class="contact-note">Update terbaru dan promo spesial</p>
                                    <a href="https://www.instagram.com/saludby.tamanide?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="contact-link" target="_blank">
                                        <i class="fab fa-instagram"></i> Follow Instagram
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Google Maps -->
                        <div class="maps-container">
                            <h3 class="mb-2">
                                <i class="fas fa-map-marker-alt"></i> Lokasi Kami
                            </h3>
                            <div class="maps-wrapper">
                                <!-- Google Maps Embed -->
                                <iframe 
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3981.984240862295!2d98.68507297582735!3d3.596241796348281!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303131c2c8c67849%3A0x589ef889cc6d80d2!2sJl.%20Kapten%20Batu%20Sihombing%202%2C%20Kenangan%2C%20Kec.%20Percut%20Sei%20Tuan%2C%20Kabupaten%20Deli%20Serdang%2C%20Sumatera%20Utara%2020371!5e0!3m2!1sid!2sid!4v1703412345678!5m2!1sid!2sid" 
                                    width="100%" 
                                    height="250" 
                                    style="border:0;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="Lokasi SALUD di Jl. Kapten Batu Sihombing 2, Deli Serdang">
                                </iframe>
                            </div>
                            <p class="address-text">
                                <i class="fas fa-map-pin"></i> Jl. Kapten Batu Sihombing 2, Kenangan, Kec. Percut Sei Tuan, Kabupaten Deli Serdang, Sumatera Utara 20371
                            </p>
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
    
    // Handle image loading untuk maps
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
});
</script>
</body>
</html>